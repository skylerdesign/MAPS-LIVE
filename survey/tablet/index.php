<?php     
require '../includes.php';
?> 
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MAPS - Measurement Assisted Practice Program System</title>

    <!-- Bootstrap Core CSS -->
    <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/calendar.css" rel="stylesheet">
    <link href="/css/bootstrap-datepicker.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
    
     <!-- DataTables CSS -->
    <link href="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery -->
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="/bower_components/metisMenu/dist/metisMenu.min.js"></script>


    <!-- Custom Theme JavaScript -->
    <script src="/dist/js/sb-admin-2.js"></script>
	
	<!-- Calendar -->
    
    
</head>
<body>
	
	
	
	
	
	<div class="container">
	    <div class="row">
		   	    </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
	            
                <div class="login-panel panel panel-default">
	                  <div style="padding: 15px 5px 5px 5px;"><p align="center"><img id="logo" width="90%" src="/survey/images/logo.png"></p></div>
                    <div class="panel-heading">
                        <h3 class="panel-title">Tablet Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post" id="form">
                            <fieldset>            
			                    <div class="form-group">
				                    <input class="form-control required" placeholder="Appointment ID" type="text" name="appointmentid" id="appointmentid" /> 
			                    </div>
			                    <div class="form-group">
			                    	<input  class="form-control" placeholder="Patient Last Name" id="lastname" /> 
			                    </div>
			                    <div class="form-group">
				                    <input type="text" class="form-control" placeholder="DOB - mm/dd/yyyy" id="dob" name="dob" data-date-format="MM/DD/YYYY">
				                    
			                    </div>
			                    <!--<div class="form-group">
			                    	<input  class="form-control" placeholder="Last Name" id="lastname" /> 
			                    </div>-->
			                    <Div class="form-group">
				                    <select class="form-control" id="type" name="type">
					                    <option>Survey Type</option>
										  <option value="http://phsweb2119.partners.org/survey/take_survey.php?x=NTQzMjI%3D">Parent Intake</option>
										  <option value="http://phsweb2119.partners.org/survey/take_survey.php?x=NTQzMjM%3D">Patient Intake</option>
										  <option value="http://phsweb2119.partners.org/survey/take_survey.php?x=NTQzMjQ%3D">Parent Follow Up</option>
										  <option value="http://phsweb2119.partners.org/survey/take_survey.php?x=NTQzMjU%3D">Patient Follow Up</option>
									</select>
			                    </Div>
			                    <input type="submit" id="btn" class="btn btn-lg btn-primary btn-block" value="Start Survey" /> 
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
	     function isDate(ExpiryDate) { 
			    var objDate,  // date object initialized from the ExpiryDate string 
			        mSeconds, // ExpiryDate in milliseconds 
			        day,      // day 
			        month,    // month 
			        year;     // year 
			    // date length should be 10 characters (no more no less) 
			    if (ExpiryDate.length !== 10) { 
			        return false; 
			    } 
			    // third and sixth character should be '/' 
			    if (ExpiryDate.substring(2, 3) !== '/' || ExpiryDate.substring(5, 6) !== '/') { 
			        return false; 
			    } 
			    // extract month, day and year from the ExpiryDate (expected format is mm/dd/yyyy) 
			    // subtraction will cast variables to integer implicitly (needed 
			    // for !== comparing) 
			    month = ExpiryDate.substring(0, 2) - 1; // because months in JS start from 0 
			    day = ExpiryDate.substring(3, 5) - 0; 
			    year = ExpiryDate.substring(6, 10) - 0; 
			    // test year range 
			    if (year < 1000 || year > 3000) { 
			        return false; 
			    } 
			    // convert ExpiryDate to milliseconds 
			    mSeconds = (new Date(year, month, day)).getTime(); 
			    // initialize Date() object from calculated milliseconds 
			    objDate = new Date(); 
			    objDate.setTime(mSeconds); 
			    // compare input date and parts from Date() object 
			    // if difference exists then date isn't valid 
			    if (objDate.getFullYear() !== year || 
			        objDate.getMonth() !== month || 
			        objDate.getDate() !== day) { 
			        return false; 
			    } 
			    // otherwise return true 
			    return true; 
			}


			     
	     
	     
	     
	     $('#btn').click(function(e) {
		     e.preventDefault();
		     var appt = $("#appointmentid").val();
		     var lastname = $("#lastname").val();
		     var dob = $("#dob").val();
		     
		     $type = $("#type").val();
		     
		     if (appt == '') {
			     alert('Please specify the appointment ID');
			     return false;
		     }
		     if (lastname == '') {
			     alert('Please specify the last name');
			     return false;
		     }
		     if (dob == '') {
			     alert('Please specify the DOB');
			     return false;
		     }
		      
		    if (isDate(dob)) { 
			    } else {
				    alert('Date format is invalid.  Please use mm/dd/yyyy format');
				    return false;
			    }
		     
		     if ($type == 'Survey Type') {
			     alert('Please specify the Survey Type');
			     return false;
		     }		     
		     
		     		     
		      // Validate user information
		     $.post("http://phsweb2119.partners.org/survey/list_users.php",
		    {
		        lastname: lastname,
		        dob: dob
		    },
		    function(data, status){
			    // Lookup user
		        $.getJSON('http://phsweb2119.partners.org/survey/list_users.php?q=' + lastname + '&dob=' + dob, function (data) {
				    $foo = 0;
				    $.each(data, function(key, val) {
				        // $(".relationships").append('<a href="/survey/patient.php?id=' + val.id + '">' + val.lastname + ', ' + val.firstname + '</a><br>');
				        // alert('its a match: ' + val.dob);
				        var keystomatch = val.lastname + '~' + val.dob;
				        //alert(keystomatch + "~~" + lastname + '~' + dob);
				        if ((val.lastname == lastname) && (val.dob == dob)) {
					        //alert('its a match: ' + keystomatch);
					        $userid = val.id;
					        $foo = 1;
				        } else {
					        
				        }
				        
				        
				        
				    });
				    
				    if ($foo == 0 ) {
					    alert('Sorry, no matches found!');
				    } else {
					   $url = $type + '?event=' + appt + '&name=' + lastname + '&patient=' + $userid;
					    window.location.href = $url;
				    }
				});
		        
		    });
		     
		     		     
		     //window.location.href = $url;
    });
	    </script>
	    
	
	
	
	<!-- Events Modal -->
	<div class="modal fade" id="events-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Appointments</h3>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
            </div>
        </div>
    </div>
</div>
	
	<!-- Morris Charts JavaScript -->
    <script src="/bower_components/raphael/raphael-min.js"></script>
    <script src="/bower_components/morrisjs/morris.min.js"></script>
    <script src="/js/morris-data.js"></script>
    
    <!-- Calendar -->
    <script src="/bower_components/underscore/underscore-min.js"></script>
    <script type="text/javascript" src="/js/calendar.js"></script>
    <script type="text/javascript">
        var calendar = $("#calendar").calendar(
            {
                tmpl_path: "/tmpls/",
                modal: "#events-modal",
                modal_type : "template",
                modal_title: function(event) { return 'asdfasdf' },
                view: 'month',
                events_source: '/appointments/events.php',
                onAfterEventsLoad: function(events) {
					if(!events) {
						return;
					}
					var list = $('#eventlist');
					list.html('');
		
					$.each(events, function(key, val) {
						$(document.createElement('li'))
							.html('<a href="' + val.url + '">' + val.title + '</a>')
							.appendTo(list);
					});
				},
				onAfterViewLoad: function(view) {
					$('.page-header h3').text(this.getTitle());
					$('.btn-group button').removeClass('active');
					$('button[data-calendar-view="' + view + '"]').addClass('active');
				},
				classes: {
					months: {
						general: 'label'
					}
				}  
            });     
            
             
             
         $('.btn-group button[data-calendar-nav]').each(function() {
				var $this = $(this);
				$this.click(function() {
					calendar.navigate($this.data('calendar-nav'));
				});
			});
		
			$('.btn-group button[data-calendar-view]').each(function() {
				var $this = $(this);
				$this.click(function() {
					calendar.view($this.data('calendar-view'));
				});
			});
		
			$('#first_day').change(function(){
				var value = $(this).val();
				value = value.length ? parseInt(value) : null;
				calendar.setOptions({first_day: value});
				calendar.view();
			});
    </script>	
	<link rel="stylesheet" type="text/css" href="/lib/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css">
<script src="/bower_components/moment/min/moment.min.js"></script>
<script src="/lib/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>


</body>

</html>