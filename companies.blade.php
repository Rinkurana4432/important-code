@extends('layouts.main')
@section('content')
        <div class="demo-parallax parallax section looking-photo nopadbot" data-stellar-background-ratio="0.5" style="background-image:url('images/skill_banner1.jpg');">
            <div class="page-title section nobg">
                <div class="container">
                    <div class="clearfix">
                        <div class="title-area pull-left">
                            <h2>For Companies <small>Please complete all fields for perfect profile..</small></h2>
                        </div>
                        <!-- /.pull-right -->
                        <div class="pull-right hidden-xs">
                            <div class="bread">
                                <ol class="breadcrumb">
                                    <li><a href="{{ url('') }}">Home</a></li>
                                   <li class="active">Company</li>
                                </ol>
                            </div>
                            <!-- end bread -->
                        </div>
                        <!-- /.pull-right -->
                    </div>
                    <!-- end clearfix -->
                </div>
            </div>
            <!-- end page-title -->
        </div>
		 <div class="section lb overflow" style="margin-top:30px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="big-title m15 text-left">
                            <h2>Free of Cost Candidates Database for Companies</h2>
                            <hr class="customhr">
                          
                        </div><!-- end title -->

                        <div class="feature-list">
                            <p>At SkillTac our prime motive is to get a Student/Fresher Placed in reputed Organizations. For this very reason we have kept our Platform completely free for partnering Companies.</p>
                            <p>SkillTac will not charge a penny from companies for accessing the student database and calling the students for Interviews. </p>
							<p>Just a confirmation from companies side is required if any student gets placed through SkillTac with them through an email. </p>
							<p>The Candidate’s Database will contain his/her contact information, residential details, qualifications and the detailed marks the particular candidate has scored through SkilTac’s E-Learning Program.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="check">
                                        <li><i class="fa fa-circle-o"></i> Free of Cost</li>
                                        <li><i class="fa fa-circle-o"></i> Large Database to Scan</li>
                                       
                                        <li><i class="fa fa-circle-o"></i>  Filtering Options to find the right candidate</li>
                                    </ul>
                                </div><!-- end col -->
                                <div class="col-md-6">
                                    <ul class="check">
                                        <li><i class="fa fa-circle-o"></i>No Vendor/Consultancy Involvement</li>
										 <li><i class="fa fa-circle-o"></i>Well Trained Candidates through our Specialized Program</li>
                                        <!--li><i class="fa fa-circle-o"></i> Building a Flexible UI</li>
                                        <li><i class="fa fa-circle-o"></i> Communicating with Other Fragments</li>
                                        <li><i class="fa fa-circle-o"></i> Supporting Different Screens</li-->
                                    </ul>
                                </div><!-- end col -->
                            </div><!-- end row -->
                            <!--<div class="large-buttons nobot">
                                <a href="{{ url('courses') }}" class="btn btn-primary btn-lg">Browse All Courses &nbsp;&nbsp;&nbsp; <i class="fa fa-long-arrow-right"></i></a>
                            </div>--><!-- end title -->
                        </div>
                    </div>
					<div class="col-md-6 m30">
						<form class="defaultform" id="form-register">
                           <div class="register-widget clearfix">
                                <div class="widget-title">
                                    <h3>Registration</h3>
                                    <hr>
                                </div><!-- end title -->
                                <div class="row">
									{{ csrf_field() }}
									<div id="result">
										
									</div>	
									<div class="subdiv">
                                    <div class="form-group col-lg-6">
                                        <label>Company name<span class="star">*</span></label>
                                        <input type="text" name="companyname" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>Company email<span class="star">*</span></label>
                                        <input type="text" name="email" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									
									</div>
									
									
									<div class="subdiv">
									  <div class="form-group col-lg-12">
                                        <label>Company Address<span class="star">*</span></label>
										<textarea cols="40" rows="10" name="companyaddress" class="form-control caddress"></textarea>
                                        <span class="spanLeft control-label"></span>
                                    </div>
									
									</div>
									<div class="subdiv">						
									<div class="form-group col-lg-6">
                                        <label>State<span class="star">*</span></label>
                                        <input type="text" name="state" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>City<span class="star">*</span></label>
                                        <input type="text" name="city" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									
									<div class="subdiv">	
									<div class="form-group col-lg-6">
                                        <label>Password<span class="star">*</span></label>
                                        <input type="password" name="password" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>Confirm Password<span class="star">*</span></label>
                                        <input type="password" name="cpassword" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									<div class="subdiv">	
									<div class="form-group col-lg-6">
                                        <label>Contact person's name</label>
                                        <input type="name" name="contactpersonname" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>Contact person's phone no<span class="star">*</span></label>
                                        <input type="name" name="contactpersonphone" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									
									<div class="subdiv">
									<div class="form-group col-lg-6">
                                        <label>Company type / Industry<span class="star">*</span></label>
                                        <!--input type="name" name="industrytype" class="form-control"-->
										<select name="industrytype" id="industrytype" class="form-control" style="max-width:510px;">
									  <option value="" selected="">Select Industry</option>
													<option value="Accounts/Finance">Accounts/Finance</option>
													<option value="Advertising/PR">Advertising/PR</option>
													<option value="Agriculture/Dairy">Agriculture/Dairy</option>
													<option value="Animation">Animation</option>
													<option value="Appliances/Electrical/Electronics">Appliances/Electrical/Electronics</option>
													<option value="Architecture/Interior Design">Architecture/Interior Design</option>
													<option value="Auto/Auto Ancillary">Auto/Auto Ancillary</option>
													<option value="Aviation/Aerospace">Aviation/Aerospace</option>
													<option value="Aviation/Hospitality/Airlines">Aviation/Hospitality/Airlines</option>
													<option value="Banking/Financial Services/Broking">Banking/Financial Services/Broking</option>
													<option value="BPO/KPO/ITES">BPO/KPO/ITES</option>
													<option value="Brewery/Distillry">Brewery/Distillry</option>
													<option value="Broking/Stock Exchange">Broking/Stock Exchange</option>
													<option value="Consulting Services">Consulting Services</option>
													<option value="eCommerce/Trading">eCommerce/Trading</option>
													<option value="Education">Education</option>
													<option value="Finance /Banking /Loan /Insurance">Finance /Banking /Loan /Insurance</option>
													<option value="FMCG">FMCG</option>
													<option value="Hardware">Hardware</option>
													<option value="Healthcare">Healthcare</option>
													<option value="HRD/Payroll/Administration/Generalist">HRD/Payroll/Administration/Generalist</option>
													<option value="Immigration">Immigration</option>
													<option value="Information Technology">Information Technology</option>
													<option value="Instrumentation">Instrumentation</option>
													<option value="Journalism/Mass Communication">Journalism/Mass Communication</option>
													<option value="Manufacturing/Fabrication">Manufacturing/Fabrication</option>
													<option value="Quality Assurance">Quality Assurance</option>
													<option value="Real Estate">Real Estate</option>
													<option value="Recruitment">Recruitment</option>
													<option value="Sales/Marketing/ Business Development">Sales/Marketing/ Business Development</option>
													<option value="Telecommunication">Telecommunication</option>
										 </select>
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>Company Logo</label>
                                        <input type="file" name="companylogo" class="form-control">
										
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									
									<div class="subdiv_chk_box">
									<div class="form-group col-lg-1">
                                      <input type="checkbox" name="chkbox" id="chkbox" class="form-control" required style="height: 30px; margin-left: -30%;">
									    <span class="spanLeft control-label"></span>	
									
                                    </div>
									<div class="form-group col-lg-5">
											<a href="{{ url('termsofusage') }}" style="text-decoration:none;"><p style="margin-top:10px;cursor:default;margin-left: -30px;">I accept the <u>Terms and Conditions</u></p></a>
									</div>	
									</div>
									 <div class="col-sm-12">
									 <!--a href="{{ url('company-profile') }}" class="btn btn-primary" onclick="register_company(this.form)">Register</a-->
                                      <button type="button" id="ajax_button" class="btn btn-primary" onclick="register_company(this.form)">Register</button>
                                    </div>
									
                                </div><!--row-->         
                            </div>
							</form>
						</div>
                </div>
            </div>
            <!--div class="macbook-wrap hidden-sm hidden-xs wow slideInRight"></div>-->
         <!--<div class="macbook-wrap1 hidden-sm hidden-xs wow slideInRight"></div>-->
			
			
</div>
        <!--<div class="section">
                <div class="container">
                <div class="container-fluid">
                        
						<div class="col-md-6 m30">
						<form class="defaultform" id="form-register">
                           <div class="register-widget clearfix">
                                <div class="widget-title">
                                    <h3>Registration</h3>
                                    <hr>
                                </div><!-- end title -->
                                <!--<div class="row">
									{{ csrf_field() }}
									<div id="result">
										
									</div>	
									<div class="subdiv">
                                    <div class="form-group col-lg-6">
                                        <label>Company name</label>
                                        <input type="text" name="companyname" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>Company email</label>
                                        <input type="text" name="email" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									
									</div>
									
									
									<div class="subdiv">
									  <div class="form-group col-lg-12">
                                        <label>Company Address</label>
										<textarea cols="40" rows="10" name="companyaddress" class="form-control caddress"></textarea>
                                        <span class="spanLeft control-label"></span>
                                    </div>
									
									</div>
									<div class="subdiv">						
									<div class="form-group col-lg-6">
                                        <label>State</label>
                                        <input type="text" name="state" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									
									<div class="subdiv">	
									<div class="form-group col-lg-6">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>Confirm Password</label>
                                        <input type="password" name="cpassword" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									<div class="subdiv">	
									<div class="form-group col-lg-6">
                                        <label>Contact person's name</label>
                                        <input type="name" name="contactpersonname" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>Contact person's phone no</label>
                                        <input type="name" name="contactpersonphone" class="form-control">
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									
									<div class="subdiv">
									<div class="form-group col-lg-6">
                                        <label>Company type / Industry</label>
                                        <!--input type="name" name="industrytype" class="form-control"-->
										<!--<select name="industrytype" id="industrytype" class="form-control" style="max-width:510px;">
									  <option value="" selected="">Select Industry</option>
													<option value="Accounts/Finance">Accounts/Finance</option>
													<option value="Advertising/PR">Advertising/PR</option>
													<option value="Agriculture/Dairy">Agriculture/Dairy</option>
													<option value="Animation">Animation</option>
													<option value="Appliances/Electrical/Electronics">Appliances/Electrical/Electronics</option>
													<option value="Architecture/Interior Design">Architecture/Interior Design</option>
													<option value="Auto/Auto Ancillary">Auto/Auto Ancillary</option>
													<option value="Aviation/Aerospace">Aviation/Aerospace</option>
													<option value="Aviation/Hospitality/Airlines">Aviation/Hospitality/Airlines</option>
													<option value="Banking/Financial Services/Broking">Banking/Financial Services/Broking</option>
													<option value="BPO/KPO/ITES">BPO/KPO/ITES</option>
													<option value="Brewery/Distillry">Brewery/Distillry</option>
													<option value="Broking/Stock Exchange">Broking/Stock Exchange</option>
													<option value="Consulting Services">Consulting Services</option>
													<option value="eCommerce/Trading">eCommerce/Trading</option>
													<option value="Education">Education</option>
													<option value="Finance /Banking /Loan /Insurance">Finance /Banking /Loan /Insurance</option>
													<option value="FMCG">FMCG</option>
													<option value="Hardware">Hardware</option>
													<option value="Healthcare">Healthcare</option>
													<option value="HRD/Payroll/Administration/Generalist">HRD/Payroll/Administration/Generalist</option>
													<option value="Immigration">Immigration</option>
													<option value="Information Technology">Information Technology</option>
													<option value="Instrumentation">Instrumentation</option>
													<option value="Journalism/Mass Communication">Journalism/Mass Communication</option>
													<option value="Manufacturing/Fabrication">Manufacturing/Fabrication</option>
													<option value="Quality Assurance">Quality Assurance</option>
													<option value="Real Estate">Real Estate</option>
													<option value="Recruitment">Recruitment</option>
													<option value="Sales/Marketing/ Business Development">Sales/Marketing/ Business Development</option>
													<option value="Telecommunication">Telecommunication</option>
										 </select>
										<span class="spanLeft control-label"></span>
                                    </div>
									<div class="form-group col-lg-6">
                                        <label>Company Logo</label>
                                        <input type="file" name="companylogo" class="form-control">
										
										<span class="spanLeft control-label"></span>
                                    </div>
									</div>
									
									<div class="subdiv_chk_box">
									<div class="form-group col-lg-1">
                                      <input type="checkbox" name="chkbox" id="chkbox" class="form-control" required style="height: 30px; margin-left: -30%;">
									    <span class="spanLeft control-label"></span>	
									
                                    </div>
									<div class="form-group col-lg-3">
											<a href="{{ url('termsofusage') }}" style="text-decoration:none;"><p style="margin-top:10px;cursor:default;margin-left: -30px;">I accept the <u>Terms and Conditions</u></p></a>
									</div>	
									</div>
									 <div class="col-sm-12">
									 <!--a href="{{ url('company-profile') }}" class="btn btn-primary" onclick="register_company(this.form)">Register</a-->
                                     <!-- <button type="button" id="ajax_button" class="btn btn-primary" onclick="register_company(this.form)">Register</button>
                                    </div>
									
                                </div><!--row-->         
                            <!--</div>
							</form>
						</div>
						
                    </div>
					   
                </div><!-- end container -->
               <!--</div>-->
			   
			   <script>
function register_company(form) {
		var error = 0;
	if(form.companyname.value.length == 0)
		{
			$(form.companyname).closest(".form-group").addClass('has-error');
			$(form.companyname).css('border', '1px solid #b94a48');
			$(form.companyname).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.companyname).closest(".form-group").removeClass('has-error');
			$(form.companyname).css('border', '1px solid #dedede');
			$(form.companyname).closest(".form-group").find("span").text('');
		}
	if(form.email.value.length == 0)
		{
			$(form.email).closest(".form-group").addClass('has-error');
			$(form.email).css('border', '1px solid #b94a48');
			$(form.email).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {		
			var x = form.email.value;
			var atpos = x.indexOf("@");
			var dotpos = x.lastIndexOf(".");
			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
				$(form.email).closest(".form-group").addClass('has-error');
				$(form.email).css('border', '1px solid #b94a48');
				$(form.email).closest(".form-group").find("span").text("Not a valid e-mail address");
				var error = 1;
			} else {
				$(form.email).closest(".form-group").removeClass('has-error');
				$(form.email).css('border', '1px solid #dedede');
				$(form.email).closest(".form-group").find("span").text('');					
			}
		}
	if(form.companyaddress.value.length == 0)
		{
			$(form.companyaddress).closest(".form-group").addClass('has-error');
			$(form.companyaddress).css('border', '1px solid #b94a48');
			$(form.companyaddress).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.companyaddress).closest(".form-group").removeClass('has-error');
			$(form.companyaddress).css('border', '1px solid #dedede');
			$(form.companyaddress).closest(".form-group").find("span").text('');
		}
	if(form.state.value.length == 0)
		{
			$(form.state).closest(".form-group").addClass('has-error');
			$(form.state).css('border', '1px solid #b94a48');
			$(form.state).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.state).closest(".form-group").removeClass('has-error');
			$(form.state).css('border', '1px solid #dedede');
			$(form.state).closest(".form-group").find("span").text('');
		}
	if(form.city.value.length == 0)
		{
			$(form.city).closest(".form-group").addClass('has-error');
			$(form.city).css('border', '1px solid #b94a48');
			$(form.city).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.city).closest(".form-group").removeClass('has-error');
			$(form.city).css('border', '1px solid #dedede');
			$(form.city).closest(".form-group").find("span").text('');
		}
	if(form.password.value.length == 0)
		{
			$(form.password).closest(".form-group").addClass('has-error');
			$(form.password).css('border', '1px solid #b94a48');
			$(form.password).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			
			if(form.password.value.length < 6) {
				$(form.password).closest(".form-group").addClass('has-error');
				$(form.password).css('border', '1px solid #b94a48');
				$(form.password).closest(".form-group").find("span").text('Enter a combination of at least six numbers or letters');
				var error = 1;			
			} else {
				$(form.password).closest(".form-group").removeClass('has-error');
				$(form.password).css('border', '1px solid #dedede');
				$(form.password).closest(".form-group").find("span").text('');				
			}

		}
	if(form.cpassword.value.length == 0)
		{
			$(form.cpassword).closest(".form-group").addClass('has-error');
			$(form.cpassword).css('border', '1px solid #b94a48');
			$(form.cpassword).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			if(form.cpassword.value.length < 6) {
				$(form.cpassword).closest(".form-group").addClass('has-error');
				$(form.cpassword).css('border', '1px solid #b94a48');
				$(form.cpassword).closest(".form-group").find("span").text('Enter a combination of at least six numbers or letters');
				var error = 1;			
			} else {
				$(form.cpassword).closest(".form-group").removeClass('has-error');
				$(form.cpassword).css('border', '1px solid #dedede');
				$(form.cpassword).closest(".form-group").find("span").text('');				
			}
		}	
	
	/*if(form.contactpersonname.value.length == 0)
		{
			$(form.contactpersonname).closest(".form-group").addClass('has-error');
			$(form.contactpersonname).css('border', '1px solid #b94a48');
			$(form.contactpersonname).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.contactpersonname).closest(".form-group").removeClass('has-error');
			$(form.contactpersonname).css('border', '1px solid #dedede');
			$(form.contactpersonname).closest(".form-group").find("span").text('');
		}*/
	
	if(form.contactpersonphone.value.length == 0)
		{
			$(form.contactpersonphone).closest(".form-group").addClass('has-error');
			$(form.contactpersonphone).css('border', '1px solid #b94a48');
			$(form.contactpersonphone).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			if(isNaN(form.contactpersonphone.value))
			{
				$(form.contactpersonphone).closest(".form-group").addClass('has-error');
				$(form.contactpersonphone).css('border', '1px solid #b94a48');
				$(form.contactpersonphone).closest(".form-group").find("span").text('Enter the valid Mobile Number(Like : 9566137117)');
				var error = 1;				
			}
			else if((form.contactpersonphone.value.length < 1) || (form.contactpersonphone.value.length > 10))
			{
				$(form.contactpersonphone).closest(".form-group").addClass('has-error');
				$(form.contactpersonphone).css('border', '1px solid #b94a48');
				$(form.contactpersonphone).closest(".form-group").find("span").text('Your Mobile Number must be 1 to 10 Integers');
				var error = 1;	
			} else {
				$(form.contactpersonphone).closest(".form-group").removeClass('has-error');
				$(form.contactpersonphone).css('border', '1px solid #dedede');
				$(form.contactpersonphone).closest(".form-group").find("span").text('');				
			}			
		}
	
	
	if(form.industrytype.value.length == 0)
		{
			$(form.industrytype).closest(".form-group").addClass('has-error');
			$(form.industrytype).css('border', '1px solid #b94a48');
			$(form.industrytype).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.industrytype).closest(".form-group").removeClass('has-error');
			$(form.industrytype).css('border', '1px solid #dedede');
			$(form.industrytype).closest(".form-group").find("span").text('');
		}
	/*if(form.companylogo.value.length == 0)
		{
			$(form.companylogo).closest(".form-group").addClass('has-error');
			$(form.companylogo).css('border', '1px solid #b94a48');
			$(form.companylogo).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.companylogo).closest(".form-group").removeClass('has-error');
			$(form.companylogo).css('border', '1px solid #dedede');
			$(form.companylogo).closest(".form-group").find("span").text('');
		}*/
	if($('#chkbox').is(":not(:checked)")){
   			$('#chkbox').closest(".form-group").addClass('has-error');
			$('#chkbox').css('border', '1px solid #b94a48');
			$('#chkbox').css('outline', '1px solid red');
			var error = 1;	
   }else {
			$('#chkbox').closest(".form-group").removeClass('has-error');
			$('#chkbox').css('border', '1px solid #dedede');
			$('#chkbox').closest(".form-group").find("span").text('');
		}
	
	
	
	if(error == 1) { 
			return false;
		} else {
			$.ajaxSetup({
				headers:
				{
					'X-CSRF-Token': $('input[name="_token"]').val()
				}
			})	

			$.ajax({
				url: "<?php echo url('companyRegister'); ?>",
				type: "POST",
				data:  new FormData(form),
				contentType: false,
				cache: false,
				processData:false,
				dataType: "html",
				beforeSend: function(){ $('#form-register #ajax_button').addClass('loadering hidder'); },	
				success: function(htmlStr)
				{		
					var json = JSON.parse(htmlStr);
				
					if(typeof(json.Result) != "undefined" && json.Result !== null) {
						$('#form-register #ajax_button').removeClass('loadering hidder').addClass('done');
						$('#form-register #ajax_button'). attr("disabled", true);
						$('#result').html('<div class="alert alert-success fade in alert-dismissable" style="margin-top:18px;"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Success!</strong> '+ json.Result +'.</div>');	
					$('#form-register').trigger("reset");
					} else {
							$('#form-register #ajax_button').removeClass('loadering hidder');
							$(form.email).closest(".form-group").addClass('has-error');
							$(form.email).css('border', '1px solid #b94a48');
							$(form.email).closest(".form-group").find("span").text(json.Error);													
					}
					
				},
				error: function() 
				{
				
				} 
			});		
		}

}

	
function login_company(form) {
		var error = 0;
		if(form.email.value.length == 0)
		{
			$(form.email).closest(".form-group").addClass('has-error');
			$(form.email).css('border', '1px solid #b94a48');
			$(form.email).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {		
			var x = form.email.value;
			var atpos = x.indexOf("@");
			var dotpos = x.lastIndexOf(".");
			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
				$(form.email).closest(".form-group").addClass('has-error');
				$(form.email).css('border', '1px solid #b94a48');
				$(form.email).closest(".form-group").find("span").text("Not a valid e-mail address");
				var error = 1;
			} else {
				$(form.email).closest(".form-group").removeClass('has-error');
				$(form.email).css('border', '1px solid #dedede');
				$(form.email).closest(".form-group").find("span").text('');					
			}
		}

		if(form.password.value.length == 0)
		{
			$(form.password).closest(".form-group").addClass('has-error');
			$(form.password).css('border', '1px solid #b94a48');
			$(form.password).closest(".form-group").find("span").text('This field is required');
			var error = 1;
		} else {
			$(form.password).closest(".form-group").removeClass('has-error');
			$(form.password).css('border', '1px solid #dedede');
			$(form.password).closest(".form-group").find("span").text('');
		}
	if(error == 1) { 
			return false;
		} else {
			$.ajaxSetup({
				headers:
				{
					'X-CSRF-Token': $('input[name="_token"]').val()
				}
			})	

			$.ajax({
				url: "<?php echo url('companylogin'); ?>",
				type: "POST",
				data:  new FormData(form),
				contentType: false,
				cache: false,
				processData:false,
				dataType: "html",
				beforeSend: function(){ $('#form-register #ajax_button').addClass('loadering hidder'); },	
				success: function(htmlStr)
				{	
				
					var json = JSON.parse(htmlStr);
				//alert(JSON.stringify(json));

				if(typeof(json.Result) != "undefined" && json.Result !== null) {
						
						$('#ajax_button1').removeClass('loadering hidder').addClass('done');
						$('#result1').html('<div class="alert alert-success fade in alert-dismissable" style="margin-top:18px;"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Success!</strong> '+ json.Result +'.</div>');	
						//window.location.href = json.url;
					    $('#loginfrm').trigger("reset");
					} else {
						$('#ajax_button1').removeClass('loadering hidder');
						if(json.type == 'username')
						{
							$(form.email).closest(".form-group").addClass('has-error');
							$(form.email).css('border', '1px solid #b94a48');
							$(form.email).closest(".form-group").find("span").text(json.Error);							
						} else if(json.type == 'password') {
							$(form.password).closest(".form-group").addClass('has-error');
							$(form.password).css('border', '1px solid #b94a48');
							$(form.password).closest(".form-group").find("span").text(json.Error);							
						} else if(json.type == 'password') {

						}
						
					}
					
					
					
					
					
				},
				error: function() 
				{
				
				} 
			});		
		}
}	
	
</script>

@endsection
					