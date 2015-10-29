<?php

include('profile-menu.php');

echo '
		<div class="wizard">
			<div class="wizard-inner col-xs-offset-2">
				<div class="connecting-line"></div>
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1" id="step1btn">
								<span class="round-tab">
									<i class="fi flaticon-playbutton17 lg"></i>
								</span>
							</a>
						</li>
						<li role="presentation" class="disabled">
							<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2" id="step2btn">
								<span class="round-tab">
									<i class="fa fa-exclamation lg"></i>
								</span>
							</a>
						</li>
						<li role="presentation" class="disabled">
							<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3" id="step3btn">
								<span class="round-tab">
									<i class="fa fa-clock-o lg"></i>
								</span>
							</a>
						</li>
						<li role="presentation" class="disabled">
							<a href="#step4" data-toggle="tab" aria-controls="complete" role="tab" title="Complete" id="step4btn" onclick="showSummary('.$id_scenario.')">
								<span class="round-tab">
									<i class="glyphicon glyphicon-ok"></i>
								</span>
							</a>
						</li>
					</ul>
				</div>
			<form role="form">
				<div class="tab-content col-xs-offset-3" id="selectElemsScenarios">
					<div class="tab-pane active col-xs-10" role="tabpanel" id="step1">
						<h3>'._('Smartcommand').'</h3>
						<p>'._('Choose the Smartcommand launched by the Scenario or Create a new one.').'</p>
						<div class="margin-bottom">
							<button type="button" class="btn btn-greenleaf"
							        onclick="createSmartcmd('.$id_scenario.')">
								'._('Create Smartcommand').'
							</button>
							<button type="button" class="btn btn-greenleaf next-step block-right"
							        onclick="saveScenarioElem(\'smartcmd\', 1)">
								'._('Save & Next Step').'
							</button>
						</div>
						<div class="list-group" id="scenario-smartcmd-list">';
						foreach ($smartcmdList as $smartcmd) {
							echo
								'<a class="list-group-item" id="scenario-smartcmd-'.$smartcmd->smartcommand_id.'"
								    onclick="selectScenarioElem('.$smartcmd->smartcommand_id.', \'smartcmd\')">
									'.$smartcmd->name.'
								</a>';
						}
						echo '
						</div>
					</div>
					<div class="tab-pane col-xs-10" role="tabpanel" id="step2">
						<h3>'._('Trigger').'</h3>
						<p>'._('Choose the Trigger which will launch the Scenario or Create a new one.').'</p>
						<div class="margin-bottom">
							<button type="button" class="btn btn-greenleaf"
							        onclick="createTrigger('.$id_scenario.')">
								'._('Create Trigger').'
							</button>
							<button type="button" class="btn btn-greenleaf next-step block-right"
							        onclick="saveScenarioElem(\'trigger\', 2)">
								'._('Save & Next Step').'
							</button>
						</div>
						<div class="list-group" id="scenario-trigger-list">';
						echo
								'<a class="list-group-item" id="scenario-trigger-0"
								    onclick="selectScenarioElem(0, \'trigger\')">
									'._('No Trigger').'
								</a>';
						foreach ($triggerList as $trigger) {
							echo
								'<a class="list-group-item" id="scenario-trigger-'.$trigger->trigger_id.'"
								    onclick="selectScenarioElem('.$trigger->trigger_id.', \'trigger\')">
									'.$trigger->name.'
								</a>';
						}
						echo '
						</div>
					</div>
					<div class="tab-pane col-xs-10" role="tabpanel" id="step3">
						<h3>'._('Schedule').'</h3>
						<p>'._('Choose the Schedule to select the time period of the Scenario or Create a new one.').'</p>
						<div class="margin-bottom">
							<button type="button" class="btn btn-greenleaf"
							        onclick="createSchedule('.$id_scenario.')">
								'._('Create Schedule').'
							</button>
							<button type="button" class="btn btn-greenleaf next-step block-right"
							        onclick="saveScenarioElem(\'schedule\', 3)">
								'._('Save & Next Step').'
							</button>
						</div>
						<div class="list-group" id="scenario-schedule-list">';
						echo
								'<a class="list-group-item" id="scenario-schedule-0"
								    onclick="selectScenarioElem(0, \'schedule\')">
									'._('No Schedule (All time)').'
								</a>';
						foreach ($scheduleList as $schedule) {
							echo
								'<a class="list-group-item" id="scenario-schedule-'.$schedule->schedule_id.'"
								    onclick="selectScenarioElem('.$schedule->schedule_id.', \'schedule\')">
									'.$schedule->name.'
								</a>';
						}
						echo '
						</div>
					</div>
					<div class="tab-pane col-xs-10" role="tabpanel" id="step4">
						<h3>'._('Summary').'</h3>
						<div id="summary-'.$id_scenario.'"></div>
						<div class="margin-bottom">
							<button type="button" class="btn btn-greenleaf next-step block-right"
							        id="completeScenarioBtn"
							        onclick="completeScenario('.$id_scenario.')"
							        disabled>
								'._('Save Scenario').'
							</button>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
		
		<script type="text/javascript">
			
			$(document).ready(function(){
				if ('.$step.' >= 1 && '.$step.' <= 4) {
					$("#step'.$step.'btn").tab(\'show\');
				}
				selectScenarioBasicElem('.$scenario_info->id_smartcmd.', \'smartcmd\');
				selectScenarioBasicElem('.$scenario_info->id_trigger.', \'trigger\');
				selectScenarioBasicElem('.$scenario_info->id_schedule.', \'schedule\');
				saveScenarioElem(\'smartcmd\', 0);
			});
			
			function selectScenarioBasicElem(id_elem, type_elem) {
				$("#scenario-"+type_elem+"-list a").removeClass("active");
				if (id_elem == 0) {
					$("#scenario-"+type_elem+"-list a:first-child").addClass(\'active\');
				}
				else {
					$("#scenario-"+type_elem+"-"+id_elem).addClass(\'active\');
				}
			}
			
			function selectScenarioElem(id_elem, type_elem) {
				$("#scenario-"+type_elem+"-list a").removeClass("active");
				$("#scenario-"+type_elem+"-"+id_elem).addClass(\'active\');
			}
			
			function saveScenarioElem(type_elem, nb_elem) {
				var id_elem;
				var nb_elem2;
			
				if (nb_elem == 0) {
					nb_elem2 = 1;
				}
				else {
					nb_elem2 = nb_elem;
				}
				id_elem = $("#scenario-"+type_elem+"-list .active").attr(\'id\').split("scenario-"+type_elem+"-")[1];
				$.ajax({
					type:"GET",
					url: "/templates/'.TEMPLATE.'/form/form_update_scenario.php",
					data: "id_scenario="+'.$id_scenario.'
							+"&id_elem="+id_elem
							+"&elem="+nb_elem2,
					success: function(result) {
						$("#step"+(nb_elem+1)+"btn").tab(\'show\');
						if(nb_elem + 1 == 4) {
							showSummary('.$id_scenario.');
						}
					}
				});
			}
			
			function showSummary(id_scenario) {
				$.ajax({
					type:"GET",
					url: "/templates/'.TEMPLATE.'/form/form_show_scenario_summary.php",
					data: "id_scenario="+'.$id_scenario.',
					success: function(result) {
						$("#summary-"+id_scenario).html(result);
					}
				});
			}

			function completeScenario(id_scenario) {
				$.ajax({
					type:"GET",
					url: "/templates/'.TEMPLATE.'/form/form_complete_scenario.php",
					data: "id_scenario="+id_scenario,
					success: function(result) {
						redirect("/profile_user_scenarios");
					}
				});
			}
		</script>';

?>