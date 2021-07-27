<?php
/**
Template Name: Services Screen Template 
**/
?>
<?php  get_header();
$user = new WP_User(get_current_user_id());
$user_id = $user->id;
$user_roles = $user->roles[0];

global $wpdb;
	$sql = $wpdb->get_results( "select problem_created_by_id, count(*) as subscriber_id From cool_web_notification where problem_created_by_id = '$user_id' AND notify_to = 'subscriber' ", ARRAY_A);
	$subscriber_id_count = '';
	foreach($sql as $subscriber_ids ){
		$subscriber_id_count .= $subscriber_ids['subscriber_id'];
	}

?>

<header class="appHeader" id="appHeader">
    <div class="HeaderTable">
        <div class="headTd">
            <div class="container"> <a href="javascript:void(0)" class="menuToggle"></a>
                <h1 class="titlePage"> <?php echo get_the_title(); ?> </h1>
				<?php if($user_roles == 'subscriber') {?>
				<div class="right_content"> 
				<div class="notification_container">
					<a href="javascript:void(0)" class="notificationIcon"><?php if(!empty($subscriber_id_count)){ ?><span class="notify_number"><?php  echo $subscriber_id_count; ?></span><?php } ?> 
				</a>
				 <ul class="notification_list">
				 <?php
				 $all_data = $wpdb->get_results("select * From cool_web_notification WHERE problem_created_by_id = '$user_id'  AND notify_to = 'subscriber' ", ARRAY_A);
				 foreach($all_data as $problems ){
				?>
					<li id="tr_<?php echo $problems['id']; ?>">
					<!--fancybox-->
					<a href="#newJobAlert" class="fancybox get_problem_id" >
						<p><span><input type="hidden" value="<?php echo $problems['problem_id']; ?>" id="problem_ids"><input type="hidden" value="<?php echo $problems['id']; ?>" id="problem_unique_id"> </span><input type="hidden" value="<?php echo $problems['notification_title'] ?>" id="notificataiontitle"><?php echo $problems['notification_body']; ?></p>
					</a>
					</li>
				<?php } ?>
				
				</ul>
					
				</div>
            <div class="buttonText"><a href="javascript:void(0)" class="backBtn"></a></div>      
        </div>
				<?php }else{ ?>
				<div class="buttonText"><a href="javascript:void(0)" class="backBtn"></a></div>   
				<?php } ?>
            </div>
        </div>
    </div>
</header>

<div class="AppTable eventPage  ">
<div class="AppTd whiteBg greyBgJobList">
    <div class="InnerPageScroll">
	<div id="msg"></div>
        <div class="JobHistoryListCont">
            <div class="container">
                <ul class="histTogaleCont JobHistoryList addnew_Data">
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="newJobAlert" id="newJobAlert">
    <div class="jobalertMid">
        <div class="newJobAlertCont">
             <div class="jobNotiAlert">
                <div class="jobAlertDetails">
                    <div class="jobAlertDetailImg" id="user_image"></div>
                    <div class="jobAlertDetailName">
                        <p id="user_name"></p>
                        <p id="user_address"></p>
                    </div>
                </div>
                <div class="jobAlertService">
                    <div class="jobAlertServices">
                        <h4>Residential Type</h4>
                        <p id="residential_type_name_id"></p>
                    </div>
                    <div class="jobAlertServices">
                        <h4>Date & Time</h4>
                        <span id="problem_date_time"></span> </div>
                    <div class="jobAlertServices">
                        <h4>Service Type</h4>
                        <p id="sub_cat_name"></p>
                    </div>
                    <div class="jobAlertServices">
                        <h4>Problem</h4>
						<p id="sub_sub_cat_name"></p>
                    </div>
					<input type="hidden" value="" id="accept_reject_problem_id">
                </div>
           </div>
        </div>
    </div>
</div>
<?php  get_footer(); ?>
<a href="#ServicePopUp" class="fancybox servPopTrigger"></a>
<div style="display:none;" class="ServicePopUp" id="ServicePopUp">
    <div class="serviceOptnBtn">
        <p>Select Your Option</p>
    </div>
    <div class="optionBtnBlock">
        <div class="popUpCancleBtn"><a href="javascript:void(0)" class="cancelServTrigger">Cancel Appointment</a>
		 
		</div>
        <div class="popUpCancleBtn popup_btn_verify"><a href="javascript:void(0)" onclick="reschdule_appointment();">Reschedule Appointment</a>
		 <input type="hidden" class="prob_ids" value=""></div>
        <div class="popUpCancleBtn Cancel_Serv_btn_Pop"><a href="javascript:void(0)" class="CancelServTriggerPop">Cancel</a></div>
    </div>
</div>
<!-- JOB IN PREOCESS POPUOP-->
<!--a href="#jobprocessPopUp" class="fancybox jobprocessTrigger"></a-->

<a href="#jobprocessPopUp" class="fancybox jobProcessTrigger"></a>

<div style="display:none;" class="jobprocessPopUp" id="jobprocessPopUp">
    <div class="jobprocessBtn">
        <p>JOB IN PROGRESS</p>
    </div>
    <div class="optionBtnBlock">
    <div class="ttxt">
		<p style="text-align:center;">Your Job is under process,contractor work on it <br/>When job done you will notify</p>
	</div>
        <a href="javascript:void(0)" class="CancelServTriggerPop"><div class="popUpCancleBtn">OK</div></a>
    </div>
</div>
<!-- JOB IN PREOCESS POPUOP-->
<!-- JOB UNDER REVIEW  -->
<a href="#reviewPopUp" class="fancybox reviewTrigger"></a>
<div style="display:none;" class="reviewPopUp" id="reviewPopUp">
    <div class="reviewBtn">
        <p style="text-align:center;font-weight:bold;">UNDER REVIEW</p>
    </div>
    <div class="optionBtnBlock">
    <div class="ttxt">
		<p style="text-align:center;">Your Job is Under contractor review <br/>When review done you will notify</p>
	</div>
        <a href="javascript:void(0)" class="CancelServTriggerPop"><div class="popUpCancleBtn">OK</div></a>
    </div>
</div>
<!-- JOB UNDER REVIEW  -->


	

<a href="#ServiceCancel" class="fancybox CancelPopTrigger"></a>
<div style="display:none;" class="ServiceCancel" id="ServiceCancel">
    <div class="serviceOptnBtn">
        <p>Cancel Appointment</p>
    </div>
    <div class="serviceOptnContent"><p>Are you sure you want to cancel appointment?<br>
You will be charged $10 for canceling this appointment?</p></div>
    <div class="optionBtnBlock">
        <div class="closePoupBtnServ"><a href="javascript:void(0)">No</a></div>
        <div class="closePoupBtnServ"><a href="javascript:void(0)" onclick="cancel_appointment();" >Yes</a>
		<input type="hidden" class="prob_ids_cancel" value="">
		
		</div>
    </div>
</div>


<script>
jQuery(document).ready(function(){
	$('.jobprocessTrigger').live("click", function(){
		$('.jobProcessTrigger').trigger('click');
	});
	
	var user_id = '<?php echo $user_id ?>';
	var roles = '<?php echo $user_roles ?>';
	var js_obj = {user_id: user_id};
    var encoded = JSON.stringify( js_obj );
	var data= encoded;
	
jQuery.ajax({
		url : "<?php echo site_url(); ?>/api/get_added_problems_by_user",
		type : "POST",
		async: false,
		data: data,
		dataType: 'text',
		success : function(response) {
		    var  obj = JSON.parse(response);
			if(obj.RespCode == '11029'){
				if(roles == 'service_provider'){
				alert('No user problems Getting !');
				var base = "<?php echo site_url(); ?>/jobs-available"; 
			    window.location.href = base;
				return false;
				}else{
					alert('No user problems Getting !');
					var base = "<?php echo site_url(); ?>/landing"; 
					window.location.href = base;
					return false;
				}
			}
			userproblems_final = obj.userproblems_final;
			//alert(JSON.stringify(userproblems_final));
			var len = userproblems_final.length;
			for(var i=0; i<len; i++){ 
                var id = userproblems_final[i].id;
				var main_cat_name = userproblems_final[i].main_cat_name;
			    var contractor_id = userproblems_final[i].contractor_id;
			    var author_id = userproblems_final[i].author_id;
				var residential_name = userproblems_final[i].residential_name;
                var date = userproblems_final[i].date;
                var time = userproblems_final[i].time;
                var sub_cat_name = userproblems_final[i].sub_cat_name;
				var sub_sub_cat_name = userproblems_final[i].sub_sub_cat_name;
				var contractor_image = userproblems_final[i].contractor_image;
				var author_image = userproblems_final[i].author_image;
				var status = userproblems_final[i].status;
				var contractor_lat = userproblems_final[i].contractor_lat;
				var contractor_long = userproblems_final[i].contractor_long;
				var problem_lat = userproblems_final[i].problem_lat;
				var problem_long = userproblems_final[i].problem_long;
			    
				if(roles == 'service_provider'){
					var problem_status_at_contractor_end = userproblems_final[i].problem_status_at_contractor_end;
					//alert(problem_status_at_contractor_end);
						if(problem_status_at_contractor_end == 'Edit Job'){
							
							var Detail = "<span id='edit_quotaion_d' onclick='goto_quotation_contractor("+id+");'class='edit_q'>Edit Quotation</span>";
							
						}else if(problem_status_at_contractor_end == 'Under Subscriber Review'){
							var Detail = '<span id="undr_review">Under Review</span>';
						}else if(problem_status_at_contractor_end == 'Subscriber Detail on Map'){
							var Detail = '<span id="dtail_id">Detail</span>';
						}else if(problem_status_at_contractor_end == 'Job In Process'){
							//var Detail = 'Job In Process';
							var Detail = "<span id='done_jobby_contractor' onclick='job_doneby_contractor("+id+");'class='edit_q'>Job In Process</span>";
						}else if(problem_status_at_contractor_end == 'FeedBack'){
							//var Detail_check = 'FeedBack';
							var Detail = "<span onclick='contractor_feedback("+id+");'class='edit_q' id='check_feedback'>FeedBack</span>";
						}else if(problem_status_at_contractor_end == 'Quotation Accepted'){
							//var Detail = 'Quote Accepted';
							var Detail = "<span onclick='quote_AcceptedBysubscriber_onContractorEnd("+id+");'class='edit_q'>Quote Accepted</span>";
						}else if(problem_status_at_contractor_end == 'Reschedule'){
							var Detail = 'Reschedule';
						}else if(problem_status_at_contractor_end == 'SendQuotation'){
							var Detail = '<span id="send_quotaion_d" onclick="send_qutation_by_contractor('+ id +')">SendQuotation</span>';
						}  
					var tr_str = '<li><div class="jbhistTop"><h2 class="jobHisTitle" >'+main_cat_name+ ' ' + id+'</h2><div class="jbhisTypes"><div class="JobtypeList"><span class="jobhisIcons CalIcon"></span><p>'+date+'</p></div><div class="JobtypeList"><span class="jobhisIcons TimeIcon"></span><p>'+time+'</p></div><div class="JobtypeList"><span class="jobhisIcons jbtypeHomeIcon"></span><p>Residential Type: '+ residential_name+'</p></div><div class="JobtypeList"><span class="jobhisIcons jbtypeActypeIcon"></span><p>'+ sub_cat_name +'</p></div><div class="JobtypeList"><span class="jobhisIcons jbtypeIcon"></span><p>'+sub_sub_cat_name +'</p></div></div><input type="hidden" value="'+ status +'" id="get_status"><input type="hidden" value="'+ problem_lat +'" id="problem_lat"><input type="hidden" value="'+ problem_long +'" id="problem_long"><input type="hidden" value="'+ contractor_lat +'" id="contractor_lat"><input type="hidden" value="'+ contractor_long +'" id="contractor_long"><input type="hidden" value="'+ author_id +'" id="ids"><input type="hidden"  value="'+id+'" id="problem_iddd"><div id="problstatus" class="problstatus">'+ Detail +'</div></div><a href="javascript:void(0);" class="appointmentscall"></a><div class="serviceditBtn"><span data-id=' + id+' class="get_id_problem" style="width:30px; height:30px; display:block; border:1px red;"></span> <a style="display:none;" href="#ServicePopUp" class="fancybox editTrigger"></a> </div><div class="remark_profile_pic" style="background-image:url('+ author_image +');"><img src="'+ author_image +'" alt="" class="mCS_img_loaded"></div></li>';
					$(".addnew_Data").append(tr_str);	
				}else if(roles == 'subscriber'){
					var problem_status_at_subscriber_end = userproblems_final[i].problem_status_at_subscriber_end;
					//alert(problem_status_at_subscriber_end);
						if(problem_status_at_subscriber_end == 'Contractor Detail on Map'){
							var Detail = '<span id="dtail_id">Detail</span>'; 
						}else if(problem_status_at_subscriber_end == 'Under contractor review'){
							var Detail = '<span id="undr_review">Under Review</span>';
						}else if(problem_status_at_subscriber_end == 'FeedBack'){
							//var Detail_check = 'FeedBack';
							var Detail = "<span onclick='subscriber_feedback("+id+");'class='edit_q' id='check_feedback_forcontractor'>FeedBack</span>";
						}else if(problem_status_at_subscriber_end == 'Quotation Accept Reject'){
							var Detail = "<span onclick='goto_quotation_subscriber("+id+");'class='edit_q'>Quotation</span>";
						}else if(problem_status_at_subscriber_end == 'Job In Process'){
							var Detail = '<a href="#jobprocessPopUp" class="jobprocessTrigger">Job In Process</a>';
						}else if(problem_status_at_subscriber_end == 'Job not started'){
							var Detail = 'Job not started';
						}else if(problem_status_at_subscriber_end == 'Reschedule'){
							var Detail = 'Reschedule';
						}else if(problem_status_at_subscriber_end == 'Reached'){
							var Detail = 'Reached';
						} 
						
						var tr_str = '<li><div class="jbhistTop"><h2 class="jobHisTitle">'+ main_cat_name+ ' ' + id+'</h2><div class="jbhisTypes"><div class="JobtypeList"><span class="jobhisIcons CalIcon"></span><p>'+date+'</p></div><div class="JobtypeList"><span class="jobhisIcons TimeIcon"></span><p>'+time+'</p></div><div class="JobtypeList"><span class="jobhisIcons jbtypeHomeIcon"></span><p>Residential Type: '+ residential_name+'</p></div><div class="JobtypeList"><span class="jobhisIcons jbtypeActypeIcon"></span><p>'+ sub_cat_name +'</p></div><div class="JobtypeList"><span class="jobhisIcons jbtypeIcon"></span><p>'+sub_sub_cat_name +'</p></div></div><input type="hidden" value="'+ status +'" id="get_status"><input type="hidden" value="'+ problem_lat +'" id="problem_lat"><input type="hidden" value="'+ problem_long +'" id="problem_long"><input type="hidden" value="'+ contractor_lat +'" id="contractor_lat"><input type="hidden" value="'+ contractor_long +'" id="contractor_long"><input type="hidden" value="'+ contractor_id +'" id="ids"><input type="hidden"  value="'+id+'" id="problem_iddd"><div id="problstatus" class="problstatus">'+ Detail +'</div></div><a href="javascript:void(0);" class="appointmentscall"></a><div class="serviceditBtn"><span data-id=' + id+' class="get_id_problem" style="width:30px; height:30px; display:block; border:1px red;"></span> <a style="display:none;" href="#ServicePopUp" class="fancybox editTrigger"></a> </div><div class="remark_profile_pic" style="background-image:url('+ contractor_image +');"><img src="'+ contractor_image +'" alt="" class="mCS_img_loaded"></div></li>';
				    $(".addnew_Data").append(tr_str);	
				}
			}
		}
	});
	jQuery('.jbhistTop').click(function(){
		
		var dtail_id = jQuery(this).find('#dtail_id').html();
		var undr_review = jQuery(this).find('#undr_review').html();
		var send_quotaion_d = jQuery(this).find('#send_quotaion_d').html();
		var edit_quotaion_d = jQuery(this).find('#edit_quotaion_d').html();
		var check_feedback = jQuery(this).find('#check_feedback').html();
		var give_feedback_forcontractor1 = jQuery(this).find('#check_feedback_forcontractor').html();
		var done_jobby_contractor2 = jQuery(this).find('#done_jobby_contractor').html();
		var click_problem_id = jQuery(this).find('#problem_iddd').val();
		if(dtail_id == 'Detail'){
		var problem_iddd = jQuery(this).find('#problem_iddd').val();
		var problem_status = jQuery(this).find('#get_status').val();
		var problem_lat = jQuery(this).find('#problem_lat').val();
		var problem_long = jQuery(this).find('#problem_long').val();
		var contractor_lat = jQuery(this).find('#contractor_lat').val();
		var contractor_long = jQuery(this).find('#contractor_long').val();
		var id = jQuery(this).find('#ids').val();
			var data_to_send = 'problem_lat=' + problem_lat + '&problem_long=' + problem_long + '&contractor_lat=' +  contractor_lat + '&contractor_long=' + contractor_long+ '&ids='+ id + '&problem_iddd=' + problem_iddd;
			var base = "<?php echo site_url(); ?>/user-details?" + data_to_send; 
			window.location.href = base;
		}else if(undr_review == 'Under Review'){
		    // alert('Under contractor review');
			jQuery('.reviewTrigger').trigger('click');
		}else if(send_quotaion_d == 'SendQuotation'){
			var data_to_send = 'gets_id=' + click_problem_id;
			var base = "<?php echo site_url(); ?>/contractor-quotation?" + data_to_send; 
		    window.location.href = base;
		   }else if(edit_quotaion_d == 'Edit Quotation'){
			   var data_to_send = 'gets_id=' + click_problem_id;
			   var base = "<?php echo site_url(); ?>/contractor-quotation?" + data_to_send; 
			   window.location.href = base;
			}else if(check_feedback == 'FeedBack'){
			   var data_to_send = 'problem_id=' + click_problem_id;
			   var base = "<?php echo site_url(); ?>/subscriber-feedback?" + data_to_send; 
			   window.location.href = base;
			}else if(give_feedback_forcontractor1 == 'FeedBack'){
			   var data_to_send = 'problem_id=' + click_problem_id;
			   var base = "<?php echo site_url(); ?>/user-feedback?" + data_to_send; 
			   window.location.href = base;
			}else if(done_jobby_contractor2 == 'Job In Process'){
				 var data_to_send = 'problem_id=' + click_problem_id;
			     var base = "<?php echo site_url(); ?>/pending-job-process?" + data_to_send; 
			     window.location.href = base;
			} 
	});
	
	
	jQuery('.serviceditBtn').click(function(){
		$('.servPopTrigger').trigger('click');
	});
	
	
	
	
	
	
});

jQuery('.get_id_problem').live("click",function(){
     jQuery('#ServicePopUp').find('.prob_ids').val('');
     jQuery('#ServiceCancel').find('.prob_ids').val('');
     var problem_id = jQuery(this).attr('data-id');
	//var problem_id =  jQuery(this).data("id")
	jQuery('#ServicePopUp').find('.prob_ids').val(problem_id);
	jQuery('#ServiceCancel').find('.prob_ids_cancel').val(problem_id);

});





function cancel_appointment(){
	var problem_id = jQuery('.prob_ids_cancel').val();
	var user_id = '<?php echo $user_id ?>';
	var user_type = '<?php echo $user_roles ?>';
	var js_obj = {problem_id:problem_id,user_type:user_type,user_id: user_id};
    var encoded = JSON.stringify( js_obj );
	var data= encoded;
	//alert(data);
	jQuery.ajax({
		url : "<?php echo site_url(); ?>/api/problem_cancelled",
		type : "POST",
		async: false,
		data: data,
		dataType: 'text',
		success : function(response) {
			var  obj = JSON.parse(response);
			if(obj.RespCode == '11026'){
			 jQuery('#msg').html(obj.Message);	
			 location.reload();	
			}
		}
	});
}

function reschdule_appointment(){
	var problem_id = jQuery('.prob_ids').val();
	var data_to_send = 'problem_id=' + problem_id;
	var base = "<?php echo site_url(); ?>/reschedule?" + data_to_send; 
	window.location.href = base;
	
}

//for Quotation accept reject by subscriber
jQuery('.get_problem_id').click(function(){
		var problmtitle = jQuery(this).find('#notificataiontitle').val();
		var problem_id = jQuery(this).find('#problem_ids').val();
		if( problmtitle == 'Service Quotation'){
			//jQuery( ".notification_list li a" ).removeClass( "fancybox" ); 
			setTimeout(function(){
				jQuery('.fancybox-close-small').trigger('click');
			},5);
			setTimeout(function(){
				jQuery('.fancybox-close-small').trigger('click');
			},10);
			var data_to_send = 'problem_id=' + problem_id;
			var base = "<?php echo site_url(); ?>/quotation?" + data_to_send; 
			window.location.href = base;
		 }else if(problmtitle == 'Problem Reschedule'){
			
			var problem_unique_id = jQuery(this).find('#problem_unique_id').val();
			var js_obj = {problem_unique_id:problem_unique_id};
			var encoded = JSON.stringify( js_obj );
			var data= encoded;
			jQuery.ajax({
				url : "<?php echo site_url(); ?>/api/reschdule_problem_datetime_for_web",
				type : "POST",
				async: false,
				data: data,
				dataType: 'text',
				success : function(response) {
					var obj = jQuery.parseJSON(response);
					date = obj.reschedule_date;
					time = obj.reschedule_time;
					var problem_id = obj.problem_id;
					residential_type_name = obj.residential_type_name;
					sub_cat_name = obj.sub_cat_name;
					sub_sub_cat_id_full = obj.sub_sub_cat_id_full;
					user_name = obj.user_name;
					userproblem_place_name = obj.userproblem_place_name;
					userproblem_place_address = obj.userproblem_place_address;
					user_image = obj.user_image;
					setTimeout(function(){
					jQuery( '.fancybox-inner #newJobAlert .newJobAlertCont #user_image_dd' ).css( 'background-image', 'url(' + user_image + ')' );
					}, 100);
					
					
					var reschdule_job_div = '<div class="jobAlertHeading">Reschedule job notification </div><div class="jobNotiAlert"><div class="jobAlertDetails"><div class="jobAlertDetailImg" id="user_image_dd" ></div><div class="jobAlertDetailName">'+user_name +'<br/>'+ userproblem_place_name  +''+ userproblem_place_address +'</div></div><div class="jobAlertService"><div class="jobAlertServices"><h4>Residential Type</h4>'+ residential_type_name +'</div><div class="jobAlertServices"><h4>Date & Time</h4><input type="hidden" id="reschedule_date" value="'+ date+'"><input type="hidden" id="reschedule_time" value="'+ time +'"><span>'+ date +' '+ time +'</span> </div><div class="jobAlertServices"><h4>Service Type</h4>'+ sub_cat_name +'</div><div class="jobAlertServices"><h4>Problem</h4>'+sub_sub_cat_id_full +'<input type="hidden" id="prob_id" value="'+ problem_id+'"></div><input type="hidden" value="" id="accept_reject_problem_id"></div><div class="popUpBtn"><div class="popUpAcceptbtn"> <a href="javascript:void(0)" onclick="reschdule_job_accept();">Reschdule Accept</a> </div><div class="popUpCancelbtn"> <a href="javascript:void(0)" onclick="reschdule_job_reject();">Reschdule Reject</a> </div></div></div>';
					setTimeout(function(){
						//alert(jQuery('.fancybox-container #newJobAlert .newJobAlertCont').length);
						jQuery('.fancybox-container #newJobAlert .newJobAlertCont').html(reschdule_job_div);
					},50);
				}
			}); 
			 
			 
		 }else if(problmtitle == 'Service Due'){
			setTimeout(function(){
				jQuery('.fancybox-close-small').trigger('click');
			},5);
			setTimeout(function(){
				jQuery('.fancybox-close-small').trigger('click');
			},10);
			 var problem_unique_id = jQuery(this).find('#problem_unique_id').val();
			 var js_obj = {problem_unique_id:problem_unique_id};
			 var encoded = JSON.stringify( js_obj );
			 var data= encoded;
			 jQuery.ajax({
				url : "<?php echo site_url(); ?>/api/delete_notification_for_web",
				type : "POST",
				async: false,
				data: data,
				dataType: 'text',
				success : function(response) {
				$("#tr_"+problem_unique_id).remove();
				location.reload();
				}
			});
			 var base = "<?php echo site_url(); ?>/landing"; 
		     window.location.href = base;
		 }else{
			 //location.reload();
			 jQuery.fancybox.close();
			setTimeout(function(){
				jQuery('.fancybox-close-small').trigger('click');
			},5);
			setTimeout(function(){
				jQuery('.fancybox-close-small').trigger('click');
			},10);
			 //jQuery( ".notification_list li a" ).removeClass( "fancybox" );
			 var problem_unique_id = jQuery(this).find('#problem_unique_id').val();
			 var js_obj = {problem_unique_id:problem_unique_id};
			 var encoded = JSON.stringify( js_obj );
			 var data= encoded;
			 jQuery.ajax({
				url : "<?php echo site_url(); ?>/api/delete_notification_for_web",
				type : "POST",
				async: false,
				data: data,
				dataType: 'text',
				success : function(response) {
				$("#tr_"+problem_unique_id).remove();
				location.reload();
				}
			});
		}
});
//for Quotation accept reject by subscriber

// function is used to go to edit quotation for contractor
function goto_quotation_contractor(id){
	var data_to_send = 'gets_id=' + id;
		var base = "<?php echo site_url(); ?>/contractor-quotation?" + data_to_send; 
		window.location.href = base;
	
}
// function is used to go to edit quotation for contractor


function send_qutation_by_contractor(id){
	var data_to_send = 'gets_id=' + id;
		var base = "<?php echo site_url(); ?>/contractor-quotation?" + data_to_send; 
		window.location.href = base;
	
}






// function is used to Check (accept Reject Quotation)  subscriber
function goto_quotation_subscriber(id){
	var data_to_send = 'problem_id=' + id;
			var base = "<?php echo site_url(); ?>/quotation?" + data_to_send; 
			window.location.href = base;
	
}
// function is used to Check (accept Reject Quotation)  subscriber

//For give feedback to contractor
function subscriber_feedback(id){
	var data_to_send = 'problem_id=' + id;
			var base = "<?php echo site_url(); ?>/user-feedback?" + data_to_send; 
			window.location.href = base;
	
}
//For give feedback to contractor
//for give feedback to subscriber

function contractor_feedback(id){
	var data_to_send = 'problem_id=' + id;
			var base = "<?php echo site_url(); ?>/subscriber-feedback?" + data_to_send; 
			window.location.href = base;
	
}
//for give feedback to subscriber





//Quote Accepted and go to started
function quote_AcceptedBysubscriber_onContractorEnd(id){
	var data_to_send = 'problem_id=' + id;
			var base = "<?php echo site_url(); ?>/start-job?" + data_to_send; 
			window.location.href = base;
}
//Quote Accepted and go to started




//JOB DONE BY Contractor
function job_doneby_contractor(id){
var data_to_send = 'problem_id=' + id;
			var base = "<?php echo site_url(); ?>/pending-job-process?" + data_to_send; 
			window.location.href = base;
}
//JOB DONE BY Contractor
//Reschdule Accept by Subscriber
function reschdule_job_accept(){
	var rescheduled_accept_reject_by_id = '<?php echo $user_id ?>';
	var problem_id = jQuery('#prob_id').val();
	var for_date = jQuery('#reschedule_date').val();
	var for_time = jQuery('#reschedule_time').val();
	var service_accept = '1';
	var js_obj = {problem_id:problem_id,for_date:for_date,for_time:for_time,rescheduled_accept_reject_by_id:rescheduled_accept_reject_by_id,service_accept:service_accept};
	var encoded = JSON.stringify( js_obj );
	var data = encoded;
	//alert(data);
	jQuery.ajax({
		url : "<?php echo site_url(); ?>/api/reschduled_accepted_rejected",
		type : "POST",
		async: false,
		data: data,
		dataType: 'text',
		success : function(response) {
		    var  obj = JSON.parse(response);
			 var base = "<?php echo site_url(); ?>/services"; 
				window.location.href = base;	
	
			}
	    });
	
	
}
//Reschdule Accept by Subscriber

//Reschdule Reject by Subscriber
function reschdule_job_reject(){
	var problem_rescheduled_by_id = '<?php echo $user_id ?>';
	var problem_id = jQuery('#prob_id').val();
	var for_date = jQuery('#reschedule_date').val();
	var for_time = jQuery('#reschedule_time').val();
	var service_accept = '0';
	var js_obj = {problem_id:problem_id,for_date:for_date,for_time:for_time,problem_rescheduled_by_id:problem_rescheduled_by_id,service_accept:service_accept};
	var encoded = JSON.stringify( js_obj );
	var data = encoded;
	
	jQuery.ajax({
		url : "<?php echo site_url(); ?>/api/reschduled_accepted_rejected",
		type : "POST",
		async: false,
		data: data,
		dataType: 'text',
		success : function(response) {
		    var  obj = JSON.parse(response);
			if(obj.RespCode == '2019'){
			    var base = "<?php echo site_url(); ?>/services"; 
				window.location.href = base;	
			}
	
			}
	    });
	
	
}

//Reschdule Reject by Subscriber




</script> 
cancelAppTrigger