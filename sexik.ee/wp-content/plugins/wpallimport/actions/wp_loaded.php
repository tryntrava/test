<?php

function pmxi_wp_loaded() {				
	
	@ini_set("max_input_time", PMXI_Plugin::getInstance()->getOption('max_input_time'));
	@ini_set("max_execution_time", PMXI_Plugin::getInstance()->getOption('max_execution_time'));		

	/* Check if cron is manualy, then execute import */
	$cron_job_key = PMXI_Plugin::getInstance()->getOption('cron_job_key');
	
	if (!empty($cron_job_key) and !empty($_GET['import_id']) and !empty($_GET['import_key']) and $_GET['import_key'] == $cron_job_key and !empty($_GET['action']) and in_array($_GET['action'], array('processing','trigger','pipe'))) {		
		
		$logger = create_function('$m', 'echo "<p>$m</p>\\n";');								

		$import = new PMXI_Import_Record();
		
		$ids = explode(',', $_GET['import_id']);

		if (!empty($ids) and is_array($ids)){			

			foreach ($ids as $id) { if (empty($id)) continue;

				$import->getById($id);	

				if ( ! $import->isEmpty() ){

					if (!in_array($import->type, array('url', 'ftp', 'file'))) {
						$logger and call_user_func($logger, sprintf(__('Scheduling update is not working with "upload" import type. Import #%s.', 'pmxi_plugin')), $id);
					}

					switch ($_GET['action']) {
						case 'trigger':
							if ( ! $import->processing ){
								$import->set(array(
									'triggered' => 1,						
									'imported' => 0,
									'created' => 0,
									'updated' => 0,
									'skipped' => 0,
									'queue_chunk_number' => 0									
								))->update();
							}
							
							$logger and call_user_func($logger, sprintf(__('#%s Cron job triggered.', 'pmxi_plugin'), $id));

							break;
						case 'processing':
							if ( $import->processing == 1 and time() - strtotime($import->registered_on) > (PMXI_Plugin::getInstance()->getOption('enable_cron_processing_time_limit')) ? PMXI_Plugin::getInstance()->getOption('cron_processing_time_limit') : 120){ // it means processor crashed, so it will reset processing to false, and terminate. Then next run it will work normally.
								$import->set(array(
									'processing' => 0
								))->update();
							}
							
							// start execution imports that is in the cron process					
							if ( (int) $import->triggered and ! (int) $import->processing ){								

								if ( function_exists('stream_context_create') )
								{									

									$opts = array(
									  'http'=>array(
									    'method'=>"GET"							   
									  )
									);

									$context = stream_context_create($opts);
									
									$fp = @fopen( home_url() .'/wp-cron.php?import_key='.$_GET['import_key'].'&import_id='.$id.'&action=pipe', 'r', false, $context);

									if ($fp === false){
										$import->execute($logger);
									}
									else{
										@fpassthru($fp);									
										@fclose($fp);									
									}										

								}
								else
								{
									$import->execute($logger);
								}

							}
							else {
								$logger and call_user_func($logger, sprintf(__('Import #%s already processing. Request skipped.', 'pmxi_plugin'), $id));								
							}

							break;					
						case 'pipe':					

							$import->execute($logger);

							break;
					}								
				}					
			}
		}
		
	}		
}