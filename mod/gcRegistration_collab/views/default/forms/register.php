<?php
/**
 * Elgg register form
 *
 * @package Elgg
 * @subpackage Core
 */

/***********************************************************************
 * MODIFICATION LOG
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *
 * USER 		DATE 			DESCRIPTION
 * TLaw/ISal 	n/a 			GC Changes
 * CYu 			March 5 2014 	Second Email field for verification & code clean up & validate email addresses
 * CYu 			July 16 2014	clearer messages & code cleanup						
 * CYu 			Sept 19 2014 	adjusted textfield rules (no spaces for emails)
 * MBlondin 	Jan 25 2016 	Layout change
 * MBlondin 	Feb 08 2016 	Delete IE7 form
 * NickP        June 9 2016     Added function to the username generation ajax to provide link to password retrival if account already exists
 * CYu 			Aug 15 2016 	GCcollab - Student / Academic (w/Universities) & Public Servants
 * MWooff 		Jan 18 2017		Re-built for GCcollab-specific functions
 *
 ***********************************************************************/

$password = $password2 = '';
$username = get_input('e');
$email = get_input('e');
$name = get_input('n');
$site_url = elgg_get_site_url();

/*if (elgg_is_sticky_form('register')) {
	extract(elgg_get_sticky_values('register'));
	elgg_clear_sticky_form('register');
}*/

// Javascript
?>
<script type="text/javascript">
$(document).ready(function() {

	$("#user_type").change(function() {
		var type = $(this).val();
		$('.occupation-choices').hide();
		if (type == 'student' || type == 'academic') {
			$('.ministry-choices').hide();
			$('#institution').show();
		} else if (type == 'federal') {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#federal').show();
		} else if (type == 'provincial') {
			$('.student-choices').hide();
			$('#provincial').show();
		} else if (type == 'municipal') {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#municipal').show();
		} else if (type == 'international') {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#international').show();
		} else {
			$('.ministry-choices').hide();
			$('.student-choices').hide();
			$('#custom').show();
		}
	});

	$("#institution-choices").change(function() {
		var type = $(this).val();
		$('.student-choices').hide();
		if (type == 'university') {
			$('#universities').show();
		} else if (type == 'college') {
			$('#colleges').show();
		}
	});

	$("#provincial-choices").change(function() {
		var type = $(this).val();
		$('.ministry-choices').hide();
		$('#' + type.replace(/\s+/g, '-').toLowerCase()).show();
	});
});

// make sure the email address given does not contain invalid characters
function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; 
    return re.test(email);
}
</script>

<!-- start of standard form -->
<div id="standard_version" class="row">

	<section class="col-md-6">
	<?php
		echo elgg_echo('gcRegister:email_notice') ;
		$js_disabled = false;
	?>
	</section>

	<?php
		function show_field( $field ){
			$enabled_fields = array('academic', 'student', 'federal', 'provincial');
			// $enabled_fields = array('academic', 'student', 'federal', 'provincial', 'municipal', 'international', 'community', 'business', 'media', 'other');
			return in_array($field, $enabled_fields);
		}
	?>

	<!-- Registration Form -->
	<section class="col-md-6">
		<div class="panel panel-default">
			<header class="panel-heading"> <h3 class="panel-title"><?php echo elgg_echo('gcRegister:form'); ?></h3> </header>
			<div class="panel-body mrgn-lft-md">

				<!-- Options for the users enabled in $enabled_fields above -->
				<div class="form-group">
					<label for="user_type" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:occupation'); ?></span></label>
					<font id="user_type_error" color="red"></font>
	    			<select id="user_type" name="user_type" class="form-control" >
	    				<?php if(show_field("federal")): ?><option selected="selected" value="federal"><?php echo elgg_echo('gcRegister:occupation:federal'); ?></option><?php endif; ?>
						<?php if(show_field("academic")): ?><option value="academic"><?php echo elgg_echo('gcRegister:occupation:academic'); ?></option><?php endif; ?>
	    				<?php if(show_field("student")): ?><option value="student"><?php echo elgg_echo('gcRegister:occupation:student'); ?></option><?php endif; ?>
	    				<?php if(show_field("provincial")): ?><option value="provincial"><?php echo elgg_echo('gcRegister:occupation:provincial'); ?></option><?php endif; ?>
	    				<?php if(show_field("municipal")): ?><option value="municipal"><?php echo elgg_echo('gcRegister:occupation:municipal'); ?></option><?php endif; ?>
	    				<?php if(show_field("international")): ?><option value="international"><?php echo elgg_echo('gcRegister:occupation:international'); ?></option><?php endif; ?>
	    				<?php if(show_field("community")): ?><option value="community"><?php echo elgg_echo('gcRegister:occupation:community'); ?></option><?php endif; ?>
	    				<?php if(show_field("business")): ?><option value="business"><?php echo elgg_echo('gcRegister:occupation:business'); ?></option><?php endif; ?>
	    				<?php if(show_field("media")): ?><option value="media"><?php echo elgg_echo('gcRegister:occupation:media'); ?></option><?php endif; ?>
	    				<?php if(show_field("other")): ?><option value="other"><?php echo elgg_echo('gcRegister:occupation:other'); ?></option><?php endif; ?>
	    			</select>
				</div>

<?php if(show_field("federal")): ?>

<?php
	$obj = elgg_get_entities(array(
	   	'type' => 'object',
	   	'subtype' => 'federal_departments',
	   	'owner_guid' => elgg_get_logged_in_user_guid()
	));
	$departments = get_entity($obj[0]->guid);
	
	$federal_departments = array();
	if (get_current_language() == 'en'){
		$federal_departments = json_decode($departments->federal_departments_en, true);
	} else {
		$federal_departments = json_decode($departments->federal_departments_fr, true);
	}

	// default to invalid value, so it encourages users to select
	$federal_choices = elgg_view('input/select', array(
		'name' => 'federal',
		'id' => 'federal-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $federal_departments),
	));
?>

				<div class="form-group occupation-choices" id="federal">
					<label for="federal-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:department'); ?></span></label>
					<?php echo $federal_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("academic") || show_field("student")): ?>

				<!-- Universities or Colleges -->
				<div class="form-group occupation-choices" id="institution" hidden>
					<label for="institution-choices" class="required"><span class="field-name"><?php echo elgg_echo('Institution'); ?></span></label>
					<select id="institution-choices" name="institution" class="form-control">
						<option selected="selected" value="default_invalid_value"> <?php echo elgg_echo('gcRegister:make_selection'); ?> </option>
						<option value="university"> <?php echo elgg_echo('gcRegister:university'); ?> </option>
						<option value="college"> <?php echo elgg_echo('gcRegister:college'); ?> </option>
					</select>
				</div>

<?php
	$universities = array("Acadia University" => "Acadia University",
	"Algoma University" => "Algoma University",
	"Athabasca University" => "Athabasca University",
	"Bishop's University" => "Bishop's University",
	"Brandon University" => "Brandon University",
	"Brescia University College" => "Brescia University College",
	"Brock University" => "Brock University",
	"Campion College" => "Campion College",
	"Canada Mennonite University" => "Canada Mennonite University",
	"Cape Breton University" => "Cape Breton University",
	"Carleton University" => "Carleton University",
	"Concordia University" => "Concordia University",
	"Concordia University of Edmonton" => "Concordia University of Edmonton",
	"Dalhousie University" => "Dalhousie University",
	"Dominican University College" => "Dominican University College",
	"École de technolgie supérieure" => "École de technolgie supérieure",
	"École des Hautes Études Commerciales de Montréal (HEC Montréal)" => "École des Hautes Études Commerciales de Montréal (HEC Montréal)",
	"École nationale d'administration publique (ENAP)" => "École nationale d'administration publique (ENAP)",
	"École Polytechnique de Montréal" => "École Polytechnique de Montréal",
	"Emily Carr University of Art and Design" => "Emily Carr University of Art and Design",
	"First Nations University of Canada" => "First Nations University of Canada",
	"Glendon College (York University)" => "Glendon College (York University)",
	"Huron University College" => "Huron University College",
	"Institut national de la recherche scientifique" => "Institut national de la recherche scientifique",
	"King's University College" => "King's University College",
	"Kwantlen Polytechnic University" => "Kwantlen Polytechnic University",
	"Lakehead University" => "Lakehead University",
	"Laurentian University" => "Laurentian University",
	"Luther College" => "Luther College",
	"MacEwan University" => "MacEwan University",
	"McGill University" => "McGill University",
	"McMaster University" => "McMaster University",
	"Memorial University" => "Memorial University",
	"Mount Allison University" => "Mount Allison University",
	"Mount Royal University" => "Mount Royal University",
	"Mount Saint Vincent University" => "Mount Saint Vincent University",
	"Nipissing University" => "Nipissing University",
	"Nova Scotia College of Art and Design University" => "Nova Scotia College of Art and Design University",
	"Ontario College of Art and Design University" => "Ontario College of Art and Design University",
	"Queen's University" => "Queen's University",
	"Redeemer University College" => "Redeemer University College",
	"Royal Military College" => "Royal Military College",
	"Royal Roads University" => "Royal Roads University",
	"Ryerson University" => "Ryerson University",
	"Saint Mary's University" => "Saint Mary's University",
	"Saint Paul University" => "Saint Paul University",
	"Simon Fraser University" => "Simon Fraser University",
	"St. Francis Xavier University" => "St. Francis Xavier University",
	"St. Jerome's University" => "St. Jerome's University",
	"St. Paul's College" => "St. Paul's College",
	"St. Thomas More College" => "St. Thomas More College",
	"St. Thomas University" => "St. Thomas University",
	"Télé-université (TÉLUQ)" => "Télé-université (TÉLUQ)",
	"The King's University" => "The King's University",
	"Thompson Rivers University" => "Thompson Rivers University",
	"Trent University" => "Trent University",
	"Trinity Western University" => "Trinity Western University",
	"Université de Moncton" => "Université de Moncton",
	"Université de Montréal" => "Université de Montréal",
	"Université de Saint-Boniface" => "Université de Saint-Boniface",
	"Université de Sherbrooke" => "Université de Sherbrooke",
	"Université du Québec" => "Université du Québec",
	"Université du Québec à Chicoutimi" => "Université du Québec à Chicoutimi",
	"Université du Québec à Montréal" => "Université du Québec à Montréal",
	"Université du Québec à Rimouski" => "Université du Québec à Rimouski",
	"Université du Québec à Trois‑Rivières" => "Université du Québec à Trois‑Rivières",
	"Université du Québec en Abitibi‑Témiscamingue" => "Université du Québec en Abitibi‑Témiscamingue",
	"Université du Québec en Outaouais" => "Université du Québec en Outaouais",
	"Université Laval" => "Université Laval",
	"Université Sainte‑Anne" => "Université Sainte‑Anne",
	"University of Alberta" => "University of Alberta",
	"University of British Columbia" => "University of British Columbia",
	"University of Calgary" => "University of Calgary",
	"University of Guelph" => "University of Guelph",
	"University of King's College" => "University of King's College",
	"University of Lethbridge" => "University of Lethbridge",
	"University of Manitoba" => "University of Manitoba",
	"University of New Brunswick" => "University of New Brunswick",
	"University of Northern British Columbia" => "University of Northern British Columbia",
	"University of Ontario Institute of Technology" => "University of Ontario Institute of Technology",
	"University of Ottawa" => "University of Ottawa",
	"University of Prince Edward Island" => "University of Prince Edward Island",
	"University of Regina" => "University of Regina",
	"University of Saskatchewan" => "University of Saskatchewan",
	"University of St. Michael's College" => "University of St. Michael's College",
	"University of Sudbury" => "University of Sudbury",
	"University of the Fraser Valley" => "University of the Fraser Valley",
	"University of Toronto" => "University of Toronto",
	"University of Trinity College" => "University of Trinity College",
	"University of Victoria" => "University of Victoria",
	"University of Waterloo" => "University of Waterloo",
	"University of Western Ontario" => "University of Western Ontario",
	"University of Windsor" => "University of Windsor",
	"University of Winnipeg" => "University of Winnipeg",
	"Vancouver Island University" => "Vancouver Island University",
	"Victoria University" => "Victoria University",
	"Wilfrid Laurier University" => "Wilfrid Laurier University",
	"York University" => "York University");

	// default to invalid value, so it encourages users to select
	$university_choices = elgg_view('input/select', array(
		'name' => 'university',
		'id' => 'university-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $universities),
	));
?>

				<!-- Universities -->
				<div class="form-group student-choices" id="universities" hidden>
					<label for="university-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:university'); ?></span></label>
					<?php echo $university_choices ?>
				</div>

<?php
	$colleges = array("Alberta College of Art and Design" => "Alberta College of Art and Design",
	"Algonquin College" => "Algonquin College",
	"Assiniboine Community College" => "Assiniboine Community College",
	"Aurora College" => "Aurora College",
	"Bow Valley College" => "Bow Valley College",
	"British Columbia Institute of Technology" => "British Columbia Institute of Technology",
	"Cambrian College of Applied Arts and Technology" => "Cambrian College of Applied Arts and Technology",
	"Camosun College" => "Camosun College",
	"Canadore College of Applied Arts and Technology" => "Canadore College of Applied Arts and Technology",
	"Capilano University" => "Capilano University",
	"Carlton Trail College" => "Carlton Trail College",
	"Cégep André-Laurendeau" => "Cégep André-Laurendeau",
	"Cégep de Chicoutimi" => "Cégep de Chicoutimi",
	"Cégep de Jonquière" => "Cégep de Jonquière",
	"Cégep de l’Abitibi-Témiscamingue" => "Cégep de l’Abitibi-Témiscamingue",
	"Cégep de la Gaspésie et des Îles" => "Cégep de la Gaspésie et des Îles",
	"Cégep de La Pocatière" => "Cégep de La Pocatière",
	"Cégep de Matane" => "Cégep de Matane",
	"Cégep de Rimouski" => "Cégep de Rimouski",
	"Cégep de Rivière-du-Loup" => "Cégep de Rivière-du-Loup",
	"Cégep de Sainte-Foy" => "Cégep de Sainte-Foy",
	"Cégep de Saint-Félicien" => "Cégep de Saint-Félicien",
	"Cégep de Saint-Laurent" => "Cégep de Saint-Laurent",
	"Cégep de Sept-Îles" => "Cégep de Sept-Îles",
	"Cégep de Sherbrooke" => "Cégep de Sherbrooke",
	"Cégep de Thetford" => "Cégep de Thetford",
	"Cégep de Trois-Rivières" => "Cégep de Trois-Rivières",
	"Cégep de Victoriaville" => "Cégep de Victoriaville",
	"Cégep Édouard-Montpetit" => "Cégep Édouard-Montpetit",
	"Cégep Garneau" => "Cégep Garneau",
	"Cégep Heritage College" => "Cégep Heritage College",
	"Cégep John Abbott College" => "Cégep John Abbott College",
	"Cégep Limoilou" => "Cégep Limoilou",
	"Cégep Marie-Victorin" => "Cégep Marie-Victorin",
	"Cégep régional de Lanaudière" => "Cégep régional de Lanaudière",
	"Cégep Saint-Jean-sur-Richelieu" => "Cégep Saint-Jean-sur-Richelieu",
	"Centennial College" => "Centennial College",
	"Centre for Nursing Studies" => "Centre for Nursing Studies",
	"Champlain Regional College" => "Champlain Regional College",
	"Collège Acadie Î.-P.-É." => "Collège Acadie Î.-P.-É.",
	"Collège André-Grasset" => "Collège André-Grasset",
	"Collège Boréal" => "Collège Boréal",
	"Collège communautaire du Nouveau-Brunswick" => "Collège communautaire du Nouveau-Brunswick",
	"Collège de Maisonneuve" => "Collège de Maisonneuve",
	"Collège Éducacentre" => "Collège Éducacentre",
	"Collège LaSalle" => "Collège LaSalle",
	"Collège Lionel-Groulx" => "Collège Lionel-Groulx",
	"Collège Mathieu" => "Collège Mathieu",
	"Collège Montmorency" => "Collège Montmorency",
	"Collège nordique francophone" => "Collège nordique francophone",
	"College of New Caledonia" => "College of New Caledonia",
	"College of the North Atlantic (CNA)" => "College of the North Atlantic (CNA)",
	"College of the Rockies" => "College of the Rockies",
	"Collège Shawinigan" => "Collège Shawinigan",
	"Conestoga College Institute of Technology and Advanced Learning" => "Conestoga College Institute of Technology and Advanced Learning",
	"Confederation College" => "Confederation College",
	"Cumberland College" => "Cumberland College",
	"Dalhousie Agricultural Campus of Dalhousie University" => "Dalhousie Agricultural Campus of Dalhousie University",
	"Douglas College" => "Douglas College",
	"Dumont Technical Institute" => "Dumont Technical Institute",
	"Durham College" => "Durham College",
	"École technique et professionnelle, Université de Saint-Boniface" => "École technique et professionnelle, Université de Saint-Boniface",
	"Emily Carr University of Art and Design" => "Emily Carr University of Art and Design",
	"Fanshawe College of Applied Arts and Technology" => "Fanshawe College of Applied Arts and Technology",
	"First Nations Technical Institute" => "First Nations Technical Institute",
	"Fleming College" => "Fleming College",
	"George Brown College" => "George Brown College",
	"Georgian College of Applied Arts and Technology" => "Georgian College of Applied Arts and Technology",
	"Grande Prairie Regional College" => "Grande Prairie Regional College",
	"Great Plains College" => "Great Plains College",
	"Holland College" => "Holland College",
	"Humber College Institute of Technology & Advanced Learning" => "Humber College Institute of Technology & Advanced Learning",
	"Institut de tourisme et d’hôtellerie du Québec" => "Institut de tourisme et d’hôtellerie du Québec",
	"Justice Institute of British Columbia" => "Justice Institute of British Columbia",
	"Kenjgewin Teg Educational Institute (KTEI)" => "Kenjgewin Teg Educational Institute (KTEI)",
	"Keyano College" => "Keyano College",
	"Kwantlen Polytechnic University" => "Kwantlen Polytechnic University",
	"La Cité" => "La Cité",
	"Lakeland College" => "Lakeland College",
	"Lambton College of Applied Arts and Technology" => "Lambton College of Applied Arts and Technology",
	"Langara College" => "Langara College",
	"Lethbridge College" => "Lethbridge College",
	"Loyalist College" => "Loyalist College",
	"Manitoba Institute of Trades and Technology" => "Manitoba Institute of Trades and Technology",
	"Marine Institute" => "Marine Institute",
	"Medicine Hat College" => "Medicine Hat College",
	"Michener Institute of Education at UHN" => "Michener Institute of Education at UHN",
	"Mohawk College" => "Mohawk College",
	"Native Education College" => "Native Education College",
	"New Brunswick College of Craft and Design" => "New Brunswick College of Craft and Design",
	"New Brunswick Community College" => "New Brunswick Community College",
	"Niagara College" => "Niagara College",
	"Nicola Valley Institute of Technology" => "Nicola Valley Institute of Technology",
	"NorQuest College" => "NorQuest College",
	"North Island College" => "North Island College",
	"North West College" => "North West College",
	"Northern Alberta Institute of Technology (NAIT)" => "Northern Alberta Institute of Technology (NAIT)",
	"Northern College" => "Northern College",
	"Northern Lakes College" => "Northern Lakes College",
	"Northern Lights College" => "Northern Lights College",
	"Northlands College" => "Northlands College",
	"Northwest Community College" => "Northwest Community College",
	"Nova Scotia Community College (NSCC)" => "Nova Scotia Community College (NSCC)",
	"Nunavut Arctic College" => "Nunavut Arctic College",
	"Okanagan College" => "Okanagan College",
	"Olds College" => "Olds College",
	"Parkland College" => "Parkland College",
	"Portage College" => "Portage College",
	"Red Deer College" => "Red Deer College",
	"Red River College of Applied Arts, Science and Technology" => "Red River College of Applied Arts, Science and Technology",
	"Saskatchewan Indian Institute of Technologies (SIIT)" => "Saskatchewan Indian Institute of Technologies (SIIT)",
	"Saskatchewan Polytechnic" => "Saskatchewan Polytechnic",
	"Sault College" => "Sault College",
	"Selkirk College" => "Selkirk College",
	"Seneca College of Applied Arts and Technology" => "Seneca College of Applied Arts and Technology",
	"Southeast College" => "Southeast College",
	"Southern Alberta Institute of Technology (SAIT)" => "Southern Alberta Institute of Technology (SAIT)",
	"St. Clair College" => "St. Clair College",
	"St. Lawrence College" => "St. Lawrence College",
	"TAV College" => "TAV College",
	"Université Sainte-Anne, Collège de l’Acadie" => "Université Sainte-Anne, Collège de l’Acadie",
	"University College of the North" => "University College of the North",
	"University of the Fraser Valley" => "University of the Fraser Valley",
	"Vancouver Community College" => "Vancouver Community College",
	"Vancouver Island University" => "Vancouver Island University",
	"Vanier College" => "Vanier College",
	"Yukon College" => "Yukon College");

	// default to invalid value, so it encourages users to select
	$college_choices = elgg_view('input/select', array(
		'name' => 'college',
		'id' => 'college-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $colleges),
	));
?>

				<!-- Colleges -->
				<div class="form-group student-choices" id="colleges" hidden>
					<label for="college-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:college'); ?></span></label>
					<?php echo $college_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("provincial")): ?>

<?php
	$provincial_departments = array();
	if (get_current_language() == 'en'){
		$provincial_departments = array("Alberta" => "Alberta",
		"British Columbia" => "British Columbia",
		"Manitoba" => "Manitoba",
		"New Brunswick" => "New Brunswick",
		"Newfoundland and Labrador" => "Newfoundland and Labrador",
		"Northwest Territories" => "Northwest Territories",
		"Nova Scotia" => "Nova Scotia",
		"Nunavut" => "Nunavut",
		"Ontario" => "Ontario",
		"Prince Edward Island" => "Prince Edward Island",
		"Quebec" => "Quebec",
		"Saskatchewan" => "Saskatchewan",
		"Yukon" => "Yukon");
	} else {
		$provincial_departments = array("Alberta" => "Alberta",
		"British Columbia" => "Colombie-Britannique",
		"Prince Edward Island" => "Île-du-Prince-Édouard",
		"Manitoba" => "Manitoba",
		"New Brunswick" => "Nouveau-Brunswick",
		"Nova Scotia" => "Nouvelle-Écosse",
		"Nunavut" => "Nunavut",
		"Ontario" => "Ontario",
		"Quebec" => "Québec",
		"Saskatchewan" => "Saskatchewan",
		"Newfoundland and Labrador" => "Terre-Neuve-et-Labrador",
		"Northwest Territories" => "Territoires du Nord-Ouest",
		"Yukon" => "Yukon");
	}

	// default to invalid value, so it encourages users to select
	$provincial_choices = elgg_view('input/select', array(
		'name' => 'provincial',
		'id' => 'provincial-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $provincial_departments),
	));
?>

				<div class="form-group occupation-choices" id="provincial" hidden>
					<label for="provincial-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:province'); ?></span></label>
					<?php echo $provincial_choices ?>
				</div>

<?php
	$alberta_ministries = array("Advanced Education" => "Advanced Education",
	"Agriculture and Forestry" => "Agriculture and Forestry",
	"Corporate Human Resourcing" => "Corporate Human Resourcing",
	"Culture and Tourism" => "Culture and Tourism",
	"Economic Development and Trade" => "Economic Development and Trade",
	"Education" => "Education",
	"Energy" => "Energy",
	"Environment and Parks" => "Environment and Parks",
	"Health" => "Health",
	"Human Services" => "Human Services",
	"Indigenous Relations" => "Indigenous Relations",
	"Infrastructure" => "Infrastructure",
	"Justice and Solicitor General" => "Justice and Solicitor General",
	"Labour" => "Labour",
	"Municipal Affairs" => "Municipal Affairs",
	"Seniors and Housing" => "Seniors and Housing",
	"Service Alberta" => "Service Alberta",
	"Status of Women" => "Status of Women",
	"Transportation" => "Transportation",
	"Treasury Board and Finance" => "Treasury Board and Finance");

	// default to invalid value, so it encourages users to select
	$alberta_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'alberta-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $alberta_ministries),
	));
?>

				<!-- Alberta -->
				<div class="form-group ministry-choices" id="alberta" hidden>
					<label for="alberta-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $alberta_choices ?>
				</div>

<?php
	$bc_ministries = array("Aboriginal Relations & Reconciliation" => "Aboriginal Relations & Reconciliation",
	"Advanced Education" => "Advanced Education",
	"Agriculture" => "Agriculture",
	"Children & Family Development" => "Children & Family Development",
	"Community, Sport & Cultural Development" => "Community, Sport & Cultural Development",
	"Education" => "Education",
	"Energy & Mines" => "Energy & Mines",
	"Environment" => "Environment",
	"Finance" => "Finance",
	"Forests, Lands & Natural Resource Operations" => "Forests, Lands & Natural Resource Operations",
	"Health" => "Health",
	"International Trade" => "International Trade",
	"Jobs, Tourism & Skills Training" => "Jobs, Tourism & Skills Training",
	"Justice" => "Justice",
	"Natural Gas Development" => "Natural Gas Development",
	"Public Safety & Solicitor General" => "Public Safety & Solicitor General",
	"Small Business, Red Tape Reduction" => "Small Business, Red Tape Reduction",
	"Social Development & Social Innovation" => "Social Development & Social Innovation",
	"Technology, Innovation & Citizens' Services" => "Technology, Innovation & Citizens' Services",
	"Transportation & Infrastructure" => "Transportation & Infrastructure");

	// default to invalid value, so it encourages users to select
	$bc_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'british-columbia-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $bc_ministries),
	));
?>

				<!-- British Columbia -->
				<div class="form-group ministry-choices" id="british-columbia" hidden>
					<label for="british-columbia-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $bc_choices ?>
				</div>

<?php
	$manitoba_ministries = array();
	if (get_current_language() == 'en'){
		$manitoba_ministries = array("Agriculture" => "Agriculture",
		"Civil Service Commission" => "Civil Service Commission",
		"Crown Services" => "Crown Services",
		"Education and Training" => "Education and Training",
		"Families" => "Families",
		"Finance" => "Finance",
		"Growth, Enterprise and Trade" => "Growth, Enterprise and Trade",
		"Health, Seniors and Active Living" => "Health, Seniors and Active Living",
		"Indigenous and Municipal Relations" => "Indigenous and Municipal Relations",
		"Infrastructure" => "Infrastructure",
		"Intergovernmental Affairs and International Relations" => "Intergovernmental Affairs and International Relations",
		"Justice" => "Justice",
		"Sport, Culture and Heritage" => "Sport, Culture and Heritage",
		"Sustainable Development" => "Sustainable Development");
	} else {
		$manitoba_ministries = array("Intergovernmental Affairs and International Relations" => "Affaires intergouvernementales et relations internationales",
		"Agriculture" => "Agriculture",
		"Civil Service Commission" => "Commission de la fonction publique",
		"Growth, Enterprise and Trade" => "Croissance, Entreprise et Commerce",
		"Sustainable Development" => "Développement durable",
		"Education and Training" => "Éducation et Formation",
		"Families" => "Familles",
		"Finance" => "Finances",
		"Infrastructure" => "Infrastructure",
		"Justice" => "Justice",
		"Indigenous and Municipal Relations" => "Relations avec les Autochtones et les municipalités",
		"Health, Seniors and Active Living" => "Santé, Aînés et Vie active",
		"Crown Services" => "Services de la Couronne",
		"Sport, Culture and Heritage" => "Sport, Culture et Patrimoine");
	}

	// default to invalid value, so it encourages users to select
	$manitoba_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'manitoba-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $manitoba_ministries),
	));
?>

				<!-- Manitoba -->
				<div class="form-group ministry-choices" id="manitoba" hidden>
					<label for="manitoba-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $manitoba_choices ?>
				</div>

<?php
	$new_brunswick_ministries = array();
	if (get_current_language() == 'en'){
		$new_brunswick_ministries = array("Aboriginal Affairs" => "Aboriginal Affairs",
		"Agriculture, Aquaculture and Fisheries" => "Agriculture, Aquaculture and Fisheries",
		"Education and Early Childhood Development" => "Education and Early Childhood Development",
		"Emergency Measures Organization" => "Emergency Measures Organization",
		"Energy and Resource Development" => "Energy and Resource Development",
		"Environment and Local Government" => "Environment and Local Government",
		"Executive Council Office" => "Executive Council Office",
		"Finance" => "Finance",
		"Health" => "Health",
		"Intergovernmental Affairs" => "Intergovernmental Affairs",
		"Justice and Public Safety" => "Justice and Public Safety",
		"Office of the Attorney General" => "Office of the Attorney General",
		"Office of the Premier" => "Office of the Premier",
		"Opportunities New Brunswick" => "Opportunities New Brunswick",
		"Post-Secondary Education, Training and Labour" => "Post-Secondary Education, Training and Labour",
		"Regional Development Corporation" => "Regional Development Corporation",
		"Service New Brunswick" => "Service New Brunswick",
		"Social Development" => "Social Development",
		"Tourism, Heritage and Culture" => "Tourism, Heritage and Culture",
		"Transportation and Infrastructure" => "Transportation and Infrastructure",
		"Treasury Board" => "Treasury Board",
		"Women's Equality" => "Women's Equality");
	} else {
		$new_brunswick_ministries = array("Aboriginal Affairs" => "Affaires autochtones",
		"Intergovernmental Affairs" => "Affaires intergouvernementales",
		"Agriculture, Aquaculture and Fisheries" => "Agriculture, Aquaculture et Pêches",
		"Executive Council Office" => "Bureau du Conseil exécutif",
		"Office of the Attorney General" => "Cabinet du procureur général",
		"Office of the Premier" => "Cabinet du premier ministre",
		"Treasury Board" => "Conseil du Trésor",
		"Energy and Resource Development" => "Développement de l'énergie et des ressources",
		"Social Development" => "Développement social",
		"Education and Early Childhood Development" => "Éducation et Développement de la petite enfance",
		"Post-Secondary Education, Training and Labour" => "Éducation postsecondaire, Formation et Travail",
		"Women's Equality" => "Égalité des femmes",
		"Environment and Local Government" => "Environnement et Gouvernements locaux",
		"Finance" => "Finances",
		"Justice and Public Safety" => "Justice et Sécurité publique",
		"Opportunities New Brunswick" => "Opportunités Nouveau-Brunswick",
		"Emergency Measures Organization" => "Organisation des mesures d'urgence",
		"Health" => "Santé",
		"Service New Brunswick" => "Service Nouveau-Brunswick",
		"Regional Development Corporation" => "Société de développement régional",
		"Tourism, Heritage and Culture" => "Tourisme, Patrimoine et Culture",
		"Transportation and Infrastructure" => "Transports et Infrastructure");
	}

	// default to invalid value, so it encourages users to select
	$new_brunswick_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'new-brunswick-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $new_brunswick_ministries),
	));
?>

				<!-- New Brunswick -->
				<div class="form-group ministry-choices" id="new-brunswick" hidden>
					<label for="new-brunswick-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $new_brunswick_choices ?>
				</div>

<?php
	$newfoundland_ministries = array("Advanced Education, Skills and Labour" => "Advanced Education, Skills and Labour",
	"Board of Commissioners of Public Utilities" => "Board of Commissioners of Public Utilities",
	"Business, Tourism, Culture and Rural Development" => "Business, Tourism, Culture and Rural Development",
	"Children, Seniors and Social Development" => "Children, Seniors and Social Development",
	"Commissioner for Legislative Standards" => "Commissioner for Legislative Standards",
	"Education and Early Childhood Development" => "Education and Early Childhood Development",
	"Electoral Districts Boundaries Commission" => "Electoral Districts Boundaries Commission",
	"Environment and Climate Change" => "Environment and Climate Change",
	"Executive Council" => "Executive Council",
	"Finance" => "Finance",
	"Fisheries, Forestry and Agrifoods" => "Fisheries, Forestry and Agrifoods",
	"Government Purchasing Agency" => "Government Purchasing Agency",
	"Health and Community Services" => "Health and Community Services",
	"Human Rights Commission" => "Human Rights Commission",
	"Justice and Public Safety" => "Justice and Public Safety",
	"Labour Relations Board" => "Labour Relations Board",
	"Multi-Materials Stewardship Board" => "Multi-Materials Stewardship Board",
	"Municipal Affairs" => "Municipal Affairs",
	"Natural Resources" => "Natural Resources",
	"Newfoundland and Labrador Film Development Corporation" => "Newfoundland and Labrador Film Development Corporation",
	"Newfoundland and Labrador Housing Corporation" => "Newfoundland and Labrador Housing Corporation",
	"Newfoundland and Labrador Hydro" => "Newfoundland and Labrador Hydro",
	"Newfoundland and Labrador Medical Care Plan - MCP" => "Newfoundland and Labrador Medical Care Plan - MCP",
	"Office of the Auditor General" => "Office of the Auditor General",
	"Office of the Chief Electoral Officer" => "Office of the Chief Electoral Officer",
	"Office of the Child and Youth Advocate" => "Office of the Child and Youth Advocate",
	"Office of the Citizens' Representative" => "Office of the Citizens' Representative",
	"Office of the Information and Privacy Commissioner" => "Office of the Information and Privacy Commissioner",
	"Provincial Information and Library Resources Board" => "Provincial Information and Library Resources Board",
	"Public Service Commission" => "Public Service Commission",
	"Research & Development Corporation" => "Research & Development Corporation",
	"Royal Newfoundland Constabulary" => "Royal Newfoundland Constabulary",
	"Service NL" => "Service NL",
	"Transportation and Works" => "Transportation and Works",
	"Workplace Health Safety and Compensation Commission" => "Workplace Health Safety and Compensation Commission",
	"Workplace Health, Safety and Compensation Review Division" => "Workplace Health, Safety and Compensation Review Division");

	// default to invalid value, so it encourages users to select
	$newfoundland_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'newfoundland-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $newfoundland_ministries),
	));
?>

				<!-- Newfoundland -->
				<div class="form-group ministry-choices" id="newfoundland-and-labrador" hidden>
					<label for="newfoundland-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $newfoundland_choices ?>
				</div>

<?php
	$northwest_territories_ministries = array();
	if (get_current_language() == 'en'){
		$northwest_territories_ministries = array("Aboriginal Affairs and Intergovernmental Relations" => "Aboriginal Affairs and Intergovernmental Relations",
		"Education, Culture and Employment" => "Education, Culture and Employment",
		"Environment and Natural Resources" => "Environment and Natural Resources",
		"Executive" => "Executive",
		"Finance" => "Finance",
		"Health and Social Services" => "Health and Social Services",
		"Human Resources" => "Human Resources",
		"Industry, Tourism and Investment" => "Industry, Tourism and Investment",
		"Justice" => "Justice",
		"Lands" => "Lands",
		"Legislative Assembly" => "Legislative Assembly",
		"Municipal and Community Affairs" => "Municipal and Community Affairs",
		"Public Works & Services" => "Public Works & Services",
		"Transportation" => "Transportation");
	} else {
		$northwest_territories_ministries = array("Aboriginal Affairs and Intergovernmental Relations" => "Ministère des Affaires autochtones et des Relations intergouvernementales",
		"Education, Culture and Employment" => "Ministère de l’Éducation, de la Culture et de la Formation",
		"Environment and Natural Resources" => "Ministère de l’Environnement et des Ressources naturelles",
		"Executive" => "Ministère de l’Exécutif",
		"Finance" => "Ministère des Finances",
		"Health and Social Services" => "Ministère de la Santé et des Services sociaux",
		"Human Resources" => "Ministère des Ressources humaines",
		"Industry, Tourism and Investment" => "Ministère de l’Industrie, du Tourisme et de l’Investissement",
		"Justice" => "Ministère de la Justice",
		"Lands" => "Ministère de l’Administration des terres",
		"Legislative Assembly" => "Assemblée législative des Territoires du Nord-Ouest",
		"Municipal and Community Affairs" => "Ministère des Affaires municipales et communautaires",
		"Public Works & Services" => "Ministère des Travaux publics et des Services",
		"Transportation" => "Ministère des Transports");
	}

	// default to invalid value, so it encourages users to select
	$northwest_territories_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'northwest-territories-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $northwest_territories_ministries),
	));
?>

				<!-- Northwest Territories -->
				<div class="form-group ministry-choices" id="northwest-territories" hidden>
					<label for="northwest-territories-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $northwest_territories_choices ?>
				</div>

<?php
	$nova_scotia_ministries = array("Aboriginal Affairs" => "Aboriginal Affairs",
	"Acadian Affairs" => "Acadian Affairs",
	"African Nova Scotian Affairs" => "African Nova Scotian Affairs",
	"Agriculture" => "Agriculture",
	"Business" => "Business",
	"Communications Nova Scotia" => "Communications Nova Scotia",
	"Communities, Culture and Heritage" => "Communities, Culture and Heritage",
	"Community Services" => "Community Services",
	"Education and Early Childhood Development" => "Education and Early Childhood Development",
	"Energy" => "Energy",
	"Environment" => "Environment",
	"Executive Council Office" => "Executive Council Office",
	"Finance and Treasury Board" => "Finance and Treasury Board",
	"Fisheries and Aquaculture" => "Fisheries and Aquaculture",
	"Gaelic Affairs" => "Gaelic Affairs",
	"Health and Wellness" => "Health and Wellness",
	"Immigration" => "Immigration",
	"Intergovernmental Affairs" => "Intergovernmental Affairs",
	"Internal Services" => "Internal Services",
	"Justice" => "Justice",
	"Labour and Advanced Education" => "Labour and Advanced Education",
	"Municipal Affairs" => "Municipal Affairs",
	"Natural Resources" => "Natural Resources",
	"Public Service Commission" => "Public Service Commission",
	"Seniors" => "Seniors",
	"Service Nova Scotia" => "Service Nova Scotia",
	"Transportation and Infrastructure Renewal" => "Transportation and Infrastructure Renewal");

	// default to invalid value, so it encourages users to select
	$nova_scotia_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'nova-scotia-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $nova_scotia_ministries),
	));
?>

				<!-- Nova Scotia -->
				<div class="form-group ministry-choices" id="nova-scotia" hidden>
					<label for="nova-scotia-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $nova_scotia_choices ?>
				</div>

<?php
	$nunavut_ministries = array();
	if (get_current_language() == 'en'){
		$nunavut_ministries = array("Community and Government Services" => "Community and Government Services",
		"Culture and Heritage" => "Culture and Heritage",
		"Economic Development and Transportation" => "Economic Development and Transportation",
		"Environment" => "Environment",
		"Education" => "Education",
		"Executive and Intergovernmental Affairs" => "Executive and Intergovernmental Affairs",
		"Family Services" => "Family Services",
		"Finance" => "Finance",
		"Health" => "Health",
		"Justice" => "Justice");
	} else {
		$nunavut_ministries = array("Culture and Heritage" => "Culture et Patrimoine",
		"Economic Development and Transportation" => "Développement économique et Transports",
		"Education" => "Éducation",
		"Environment" => "Environnement",
		"Executive and Intergovernmental Affairs" => "Exécutif et Affaires intergouvernementales",
		"Finance" => "Finances",
		"Justice" => "Justice",
		"Health" => "Santé",
		"Family Services" => "Services à la famille",
		"Community and Government Services" => "Services communautaires et gouvernementaux");
	}

	// default to invalid value, so it encourages users to select
	$nunavut_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'nunavut-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $nunavut_ministries),
	));
?>

				<!-- Nunavut -->
				<div class="form-group ministry-choices" id="nunavut" hidden>
					<label for="nunavut-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $nunavut_choices ?>
				</div>

<?php
	$ontario_ministries = array();
	if (get_current_language() == 'en'){
		$ontario_ministries = array("Accessibility Directorate of Ontario" => "Accessibility Directorate of Ontario",
		"Advanced Education and Skills Development" => "Advanced Education and Skills Development",
		"Agriculture, Food and Rural Affairs" => "Agriculture, Food and Rural Affairs",
		"Attorney General" => "Attorney General",
		"Children and Youth Services" => "Children and Youth Services",
		"Citizenship and Immigration" => "Citizenship and Immigration",
		"Community and Social Services" => "Community and Social Services",
		"Community Safety and Correctional Services" => "Community Safety and Correctional Services",
		"Economic Development and Growth" => "Economic Development and Growth",
		"Education" => "Education",
		"Energy" => "Energy",
		"Environment and Climate Change" => "Environment and Climate Change",
		"Finance" => "Finance",
		"Francophone Affairs" => "Francophone Affairs",
		"Government and Consumer Services" => "Government and Consumer Services",
		"Health and Long-Term Care" => "Health and Long-Term Care",
		"Housing" => "Housing",
		"Infrastructure" => "Infrastructure",
		"International Trade" => "International Trade",
		"Labour" => "Labour",
		"Municipal Affairs" => "Municipal Affairs",
		"Natural Resources and Forestry" => "Natural Resources and Forestry",
		"Northern Development and Mines" => "Northern Development and Mines",
		"Research, Innovation and Science" => "Research, Innovation and Science",
		"Seniors" => "Seniors",
		"Tourism, Culture and Sport" => "Tourism, Culture and Sport",
		"Transportation" => "Transportation",
		"Treasury Board Secretariat" => "Treasury Board Secretariat");
	} else {
		$ontario_ministries = array("Seniors" => "Affaires des personnes âgées",
		"Francophone Affairs" => "Affaires francophones",
		"International Trade" => "Commerce international",
		"Accessibility Directorate of Ontario" => "Direction générale de l'accessibilité",
		"Advanced Education and Skills Development" => "L’Enseignement supérieur et de la formation professionnelle",
		"Research, Innovation and Science" => "La Recherche, de l’innovation et des sciences",
		"Infrastructure" => "Ministère de l’infrastructure",
		"Health and Long-Term Care" => "Ministère de la santé et des soins de longue durée",
		"Community Safety and Correctional Services" => "Ministère de la sécurité communautaire et des services correctionnels",
		"Agriculture, Food and Rural Affairs" => "Ministère de l'agriculture, de l'alimentation et des affaires rurales",
		"Education" => "Ministère de l'éducation",
		"Energy" => "Ministère de l'énergie",
		"Environment and Climate Change" => "Ministère de l'environnement et de l'action en matière de changement climatique",
		"Citizenship and Immigration" => "Ministère des affaires civiques et de l'immigration",
		"Municipal Affairs" => "Ministère des affaires municipales",
		"Finance" => "Ministère des finances",
		"Natural Resources and Forestry" => "Ministère des richesses naturelles et des forêts",
		"Children and Youth Services" => "Ministère des services à l'enfance et à la jeunesse",
		"Government and Consumer Services" => "Ministère des services gouvernementaux et des services aux consommateurs",
		"Community and Social Services" => "Ministère des services sociaux et communautaires",
		"Transportation" => "Ministère des transports",
		"Northern Development and Mines" => "Ministère du développement du nord et des mines",
		"Economic Development and Growth" => "Ministère du développement économique et de la croissance",
		"Housing" => "Ministère du logement",
		"Attorney General" => "Ministère du procureur général",
		"Tourism, Culture and Sport" => "Ministère du tourisme, de la culture et du sport",
		"Labour" => "Ministère du travail",
		"Treasury Board Secretariat" => "Secrétariat du conseil du trésor");
	}

	// default to invalid value, so it encourages users to select
	$ontario_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'ontario-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $ontario_ministries),
	));
?>

				<!-- Ontario -->
				<div class="form-group ministry-choices" id="ontario" hidden>
					<label for="ontario-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $ontario_choices ?>
				</div>

<?php
	$pei_ministries = array();
	if (get_current_language() == 'en'){
		$pei_ministries = array("Agriculture and Fisheries" => "Agriculture and Fisheries",
		"Communities, Land and Environment" => "Communities, Land and Environment",
		"Economic Development and Tourism" => "Economic Development and Tourism",
		"Education, Early Learning and Culture" => "Education, Early Learning and Culture",
		"Family and Human Services" => "Family and Human Services",
		"Finance" => "Finance",
		"Health and Wellness" => "Health and Wellness",
		"Justice and Public Safety" => "Justice and Public Safety",
		"Transportation, Infrastructure and Energy" => "Transportation, Infrastructure and Energy",
		"Workforce and Advanced Learning" => "Workforce and Advanced Learning");
	} else {
		$pei_ministries = array("Agriculture and Fisheries" => "Agriculture et Pêches",
		"Communities, Land and Environment" => "Communautés, Terres et Environnement",
		"Economic Development and Tourism" => "Développement économique et Tourisme",
		"Education, Early Learning and Culture" => "Éducation, Développement préscolaire et Culture",
		"Finance" => "Finances",
		"Justice and Public Safety" => "Justice et Sécurité publique",
		"Workforce and Advanced Learning" => "Main-d’œuvre et Études supérieures",
		"Health and Wellness" => "Santé et Mieux-être",
		"Family and Human Services" => "Services à la famille et à la personne",
		"Transportation, Infrastructure and Energy" => "Transports, Infrastructure et Énergie");
	}

	// default to invalid value, so it encourages users to select
	$pei_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'pei-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $pei_ministries),
	));
?>

				<!-- PEI -->
				<div class="form-group ministry-choices" id="prince-edward-island" hidden>
					<label for="pei-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $pei_choices ?>
				</div>

<?php
	$quebec_ministries = array("Agence de la santé et des services sociaux de Chaudière-Appalaches" => "Agence de la santé et des services sociaux de Chaudière-Appalaches",
	"Agence de la santé et des services sociaux de la Capitale-Nationale" => "Agence de la santé et des services sociaux de la Capitale-Nationale",
	"Agence de la santé et des services sociaux de la Côte-Nord" => "Agence de la santé et des services sociaux de la Côte-Nord",
	"Agence de la santé et des services sociaux de la Gaspésie-Iles-de-la-Madeleine" => "Agence de la santé et des services sociaux de la Gaspésie-Iles-de-la-Madeleine",
	"Agence de la santé et des services sociaux de la Mauricie et du Centre-du-Québecc" => "Agence de la santé et des services sociaux de la Mauricie et du Centre-du-Québecc",
	"Agence de la santé et des services sociaux de l'Abitibi-Témiscamingue" => "Agence de la santé et des services sociaux de l'Abitibi-Témiscamingue",
	"Agence de la santé et des services sociaux de Lanaudière" => "Agence de la santé et des services sociaux de Lanaudière",
	"Agence de la santé et des services sociaux de Laval" => "Agence de la santé et des services sociaux de Laval",
	"Agence de la santé et des services sociaux de l'Estrie" => "Agence de la santé et des services sociaux de l'Estrie",
	"Agence de la santé et des services sociaux de l'Outaouais" => "Agence de la santé et des services sociaux de l'Outaouais",
	"Agence de la santé et des services sociaux de Montréal" => "Agence de la santé et des services sociaux de Montréal",
	"Agence de la santé et des services sociaux des Laurentides" => "Agence de la santé et des services sociaux des Laurentides",
	"Agence de la santé et des services sociaux du Bas-Saint-Laurent" => "Agence de la santé et des services sociaux du Bas-Saint-Laurent",
	"Agence de la santé et des services sociaux du Saguenay–Lac-Saint-Jean" => "Agence de la santé et des services sociaux du Saguenay–Lac-Saint-Jean",
	"Agence métropolitaine de transport" => "Agence métropolitaine de transport",
	"Aide financière aux études" => "Aide financière aux études",
	"Assemblée nationale du Québec" => "Assemblée nationale du Québec",
	"Autorité des marchés financiers" => "Autorité des marchés financiers",
	"Bibliothèque et Archives nationales du Québec" => "Bibliothèque et Archives nationales du Québec",
	"Bureau d'audiences publiques sur l'environnement" => "Bureau d'audiences publiques sur l'environnement",
	"Bureau de normalisation du Québec" => "Bureau de normalisation du Québec",
	"Bureau des infractions et amendes" => "Bureau des infractions et amendes",
	"Bureau du coroner" => "Bureau du coroner",
	"Bureau du forestier en chef" => "Bureau du forestier en chef",
	"Caisse de dépôt et placement du Québec" => "Caisse de dépôt et placement du Québec",
	"Centre de Conservation du Québec" => "Centre de Conservation du Québec",
	"Centre de gestion de l'équipement roulant" => "Centre de gestion de l'équipement roulant",
	"Centre de la francophonie des Amériques" => "Centre de la francophonie des Amériques",
	"Centre de recherche industrielle du Québec" => "Centre de recherche industrielle du Québec",
	"Centre de services partagés du Québec" => "Centre de services partagés du Québec",
	"Centre de toxicologie du Québec" => "Centre de toxicologie du Québec",
	"Centre d'étude sur la pauvreté et l'exclusion sociale" => "Centre d'étude sur la pauvreté et l'exclusion sociale",
	"Centre d'expertise des grands organismes" => "Centre d'expertise des grands organismes",
	"Centre d'expertise hydrique du Québec" => "Centre d'expertise hydrique du Québec",
	"Centre intégré de santé et de services sociaux de la Montérégie-Centre" => "Centre intégré de santé et de services sociaux de la Montérégie-Centre",
	"Centre intégré de santé et de services sociaux de la Montérégie–Centre" => "Centre intégré de santé et de services sociaux de la Montérégie–Centre",
	"Centre intégré de santé et de services sociaux de la Montérégie-Ouest" => "Centre intégré de santé et de services sociaux de la Montérégie-Ouest",
	"Centre intégré de santé et de services sociaux des Îles" => "Centre intégré de santé et de services sociaux des Îles",
	"Centre intégré universitaire de santé et de services sociaux du Nord-de-l'Île-de-Montréal" => "Centre intégré universitaire de santé et de services sociaux du Nord-de-l'Île-de-Montréal",
	"Centre intégré universitaire de santé et services sociaux de l'Est-de-l'Île-de-Montréal" => "Centre intégré universitaire de santé et services sociaux de l'Est-de-l'Île-de-Montréal",
	"Centre intégré universitaire du Centre-Est-de-l'Île-de-Montréal" => "Centre intégré universitaire du Centre-Est-de-l'Île-de-Montréal",
	"Centre intégré universitaire du Centre-Ouest-de-l'Île-de-Montréal" => "Centre intégré universitaire du Centre-Ouest-de-l'Île-de-Montréal",
	"Centre local de services communautaires" => "Centre local de services communautaires",
	"Centre régional de santé et de services sociaux de la Baie-James" => "Centre régional de santé et de services sociaux de la Baie-James",
	"Comité consultatif du travail et de la main-d'œuvre" => "Comité consultatif du travail et de la main-d'œuvre",
	"Comité consultatif sur l'accessibilité financière aux études" => "Comité consultatif sur l'accessibilité financière aux études",
	"Comité de déontologie policière" => "Comité de déontologie policière",
	"Comité pour la prestation des services de santé et des services sociaux aux personnes issues des communautés ethnoculturelles" => "Comité pour la prestation des services de santé et des services sociaux aux personnes issues des communautés ethnoculturelles",
	"Commissaire à la déontologie policière" => "Commissaire à la déontologie policière",
	"Commissaire à la lutte contre la corruption" => "Commissaire à la lutte contre la corruption",
	"Commissaire à la santé et au bien-être" => "Commissaire à la santé et au bien-être",
	"Commissaire à l'éthique et à la déontologie" => "Commissaire à l'éthique et à la déontologie",
	"Commissaire au lobbyisme" => "Commissaire au lobbyisme",
	"Commission consultative de l'enseignement privé" => "Commission consultative de l'enseignement privé",
	"Commission d'accès à l'information" => "Commission d'accès à l'information",
	"Commission de la capitale nationale du Québec" => "Commission de la capitale nationale du Québec",
	"Commission de la construction du Québec" => "Commission de la construction du Québec",
	"Commission de la fonction publique" => "Commission de la fonction publique",
	"Commission de la qualité de l'environnement Kativik" => "Commission de la qualité de l'environnement Kativik",
	"Commission de la représentation électorale" => "Commission de la représentation électorale",
	"Commission de l'éducation en langue anglaise" => "Commission de l'éducation en langue anglaise",
	"Commission de l'éthique de la science et de la technologie" => "Commission de l'éthique de la science et de la technologie",
	"Commission de protection du territoire agricole du Québec" => "Commission de protection du territoire agricole du Québec",
	"Commission de toponymie" => "Commission de toponymie",
	"Commission d'enquête sur l’octroi et la gestion des contrats publics dans l’industrie de la construction" => "Commission d'enquête sur l’octroi et la gestion des contrats publics dans l’industrie de la construction",
	"Commission des droits de la personne et des droits de la jeunesse" => "Commission des droits de la personne et des droits de la jeunesse",
	"Commission des normes, de l'équité, de la santé et de la sécurité du travail" => "Commission des normes, de l'équité, de la santé et de la sécurité du travail",
	"Commission des partenaires du marché du travail" => "Commission des partenaires du marché du travail",
	"Commission des services juridiques" => "Commission des services juridiques",
	"Commission des transports du Québec" => "Commission des transports du Québec",
	"Commission des valeurs mobilières du Québec (voir Autorité des marchés financiers)" => "Commission des valeurs mobilières du Québec (voir Autorité des marchés financiers)",
	"Commission d'évaluation de l'enseignement collégial" => "Commission d'évaluation de l'enseignement collégial",
	"Commission municipale du Québec" => "Commission municipale du Québec",
	"Commission québécoise des libérations conditionnelles" => "Commission québécoise des libérations conditionnelles",
	"Conseil consultatif de la lecture et du livre" => "Conseil consultatif de la lecture et du livre",
	"Conseil cri de la santé et des services sociaux de la Baie James" => "Conseil cri de la santé et des services sociaux de la Baie James",
	"Conseil de gestion de l'assurance parentale" => "Conseil de gestion de l'assurance parentale",
	"Conseil de la justice administrative" => "Conseil de la justice administrative",
	"Conseil de la magistrature du Québec" => "Conseil de la magistrature du Québec",
	"Conseil de l'Ordre du Québec" => "Conseil de l'Ordre du Québec",
	"Conseil des appellations réservées et des termes valorisants" => "Conseil des appellations réservées et des termes valorisants",
	"Conseil des arts et des lettres du Québec" => "Conseil des arts et des lettres du Québec",
	"Conseil du statut de la femme" => "Conseil du statut de la femme",
	"Conseil supérieur de la langue française" => "Conseil supérieur de la langue française",
	"Conseil supérieur de l'éducation" => "Conseil supérieur de l'éducation",
	"Conseils régionaux des partenaires du marché du travail" => "Conseils régionaux des partenaires du marché du travail",
	"Conservatoire de musique et d'art dramatique du Québec" => "Conservatoire de musique et d'art dramatique du Québec",
	"Corporation d'urgence-santé" => "Corporation d'urgence-santé",
	"Cour d'appel du Québec" => "Cour d'appel du Québec",
	"Cour du Québec" => "Cour du Québec",
	"Cour supérieure du Québec" => "Cour supérieure du Québec",
	"Curateur public du Québec" => "Curateur public du Québec",
	"Directeur de l'état civil" => "Directeur de l'état civil",
	"Directeur des poursuites criminelles et pénales" => "Directeur des poursuites criminelles et pénales",
	"Directeur général des élections du Québec" => "Directeur général des élections du Québec",
	"École nationale de police du Québec" => "École nationale de police du Québec",
	"École nationale des pompiers du Québec" => "École nationale des pompiers du Québec",
	"Emploi-Québec" => "Emploi-Québec",
	"Épargne Placements Québec" => "Épargne Placements Québec",
	"Financière agricole du Québec" => "Financière agricole du Québec",
	"Fondation de la faune du Québec" => "Fondation de la faune du Québec",
	"Fonds d'aide aux recours collectifs" => "Fonds d'aide aux recours collectifs",
	"Fonds de la recherche en santé du Québec" => "Fonds de la recherche en santé du Québec",
	"Fonds de recherche du Québec – Scientifique en chef" => "Fonds de recherche du Québec – Scientifique en chef",
	"Fonds québécois de la recherche sur la nature et les technologies" => "Fonds québécois de la recherche sur la nature et les technologies",
	"Fonds québécois de la recherche sur la société et la culture" => "Fonds québécois de la recherche sur la société et la culture",
	"Héma-Québec" => "Héma-Québec",
	"Hydro-Québec" => "Hydro-Québec",
	"Indemnisation des victimes d’actes criminels" => "Indemnisation des victimes d’actes criminels",
	"Institut de la statistique du Québec" => "Institut de la statistique du Québec",
	"Institut de tourisme et d'hôtellerie du Québec" => "Institut de tourisme et d'hôtellerie du Québec",
	"Institut national de santé publique du Québec" => "Institut national de santé publique du Québec",
	"Institut national des mines" => "Institut national des mines",
	"Institut national d'excellence en santé et en services sociaux" => "Institut national d'excellence en santé et en services sociaux",
	"Investissement Québec" => "Investissement Québec",
	"La Financière agricole du Québec - Développement international" => "La Financière agricole du Québec - Développement international",
	"Les Publications du Québec" => "Les Publications du Québec",
	"Ministère de l’Éducation et de l’Enseignement supérieur" => "Ministère de l’Éducation et de l’Enseignement supérieur",
	"Ministère de la Culture et des Communications" => "Ministère de la Culture et des Communications",
	"Ministère de la Famille" => "Ministère de la Famille",
	"Ministère de la Justice" => "Ministère de la Justice",
	"Ministère de la Santé et des Services sociaux" => "Ministère de la Santé et des Services sociaux",
	"Ministère de la Sécurité publique" => "Ministère de la Sécurité publique",
	"Ministère de l'Agriculture, des Pêcheries et de l'Alimentation" => "Ministère de l'Agriculture, des Pêcheries et de l'Alimentation",
	"Ministère de l'Économie, de la Science et de l'Innovation" => "Ministère de l'Économie, de la Science et de l'Innovation",
	"Ministère de l'Énergie et des Ressources naturelles" => "Ministère de l'Énergie et des Ressources naturelles",
	"Ministère de l'Immigration, de la Diversité et de l'Inclusion" => "Ministère de l'Immigration, de la Diversité et de l'Inclusion",
	"Ministère des Affaires municipales et de l'Occupation du territoire" => "Ministère des Affaires municipales et de l'Occupation du territoire",
	"Ministère des Finances" => "Ministère des Finances",
	"Ministère des Forêts, de la Faune et des Parcs" => "Ministère des Forêts, de la Faune et des Parcs",
	"Ministère des Relations internationales et de la Francophonie" => "Ministère des Relations internationales et de la Francophonie",
	"Ministère des Transports, de la Mobilité durable et de l'Électrification des transports" => "Ministère des Transports, de la Mobilité durable et de l'Électrification des transports",
	"Ministère du Conseil exécutif" => "Ministère du Conseil exécutif",
	"Ministère du Développement durable, de l'Environnement et de la Lutte contre les changements climatiques" => "Ministère du Développement durable, de l'Environnement et de la Lutte contre les changements climatiques",
	"Ministère du Tourisme" => "Ministère du Tourisme",
	"Ministère du Travail, de l'Emploi et de la Solidarité sociale" => "Ministère du Travail, de l'Emploi et de la Solidarité sociale",
	"Musée d'art contemporain de Montréal" => "Musée d'art contemporain de Montréal",
	"Musée de la civilisation" => "Musée de la civilisation",
	"Musée de la Place royale" => "Musée de la Place royale",
	"Musée de l'Amérique francophone" => "Musée de l'Amérique francophone",
	"Musée national des beaux-arts du Québec" => "Musée national des beaux-arts du Québec",
	"Office de la protection du consommateur" => "Office de la protection du consommateur",
	"Office de la Sécurité du revenu des chasseurs et piégeurs cris" => "Office de la Sécurité du revenu des chasseurs et piégeurs cris",
	"Office des personnes handicapées du Québec" => "Office des personnes handicapées du Québec",
	"Office des professions du Québec" => "Office des professions du Québec",
	"Office franco-québécois pour la jeunesse" => "Office franco-québécois pour la jeunesse",
	"Office Québec-Monde pour la jeunesse" => "Office Québec-Monde pour la jeunesse",
	"Office québécois de la langue française" => "Office québécois de la langue française",
	"Palais des congrès de Montréal" => "Palais des congrès de Montréal",
	"Protecteur du citoyen" => "Protecteur du citoyen",
	"RECYC-QUÉBEC" => "RECYC-QUÉBEC",
	"Régie de l'assurance maladie du Québec" => "Régie de l'assurance maladie du Québec",
	"Régie de l'assurance-dépôts du Québec (voir Autorité des marchés financiers)" => "Régie de l'assurance-dépôts du Québec (voir Autorité des marchés financiers)",
	"Régie de l'énergie" => "Régie de l'énergie",
	"Régie des alcools, des courses et des jeux" => "Régie des alcools, des courses et des jeux",
	"Régie des installations olympiques" => "Régie des installations olympiques",
	"Régie des marchés agricoles et alimentaires du Québec" => "Régie des marchés agricoles et alimentaires du Québec",
	"Régie du bâtiment du Québec" => "Régie du bâtiment du Québec",
	"Régie du Cinéma" => "Régie du Cinéma",
	"Régie du logement" => "Régie du logement",
	"Registraire des entreprises" => "Registraire des entreprises",
	"Registre des droits personnels et réels mobiliers" => "Registre des droits personnels et réels mobiliers",
	"Registre des lobbyistes" => "Registre des lobbyistes",
	"Registre foncier du Québec" => "Registre foncier du Québec",
	"Retraite Québec" => "Retraite Québec",
	"Revenu Québec" => "Revenu Québec",
	"Secrétariat à la condition féminine" => "Secrétariat à la condition féminine",
	"Secrétariat à la jeunesse" => "Secrétariat à la jeunesse",
	"Secrétariat à la politique linguistique" => "Secrétariat à la politique linguistique",
	"Secrétariat à l'accès à l'information et à la réforme des institutions démocratiques" => "Secrétariat à l'accès à l'information et à la réforme des institutions démocratiques",
	"Secrétariat aux affaires autochtones" => "Secrétariat aux affaires autochtones",
	"Secrétariat aux affaires intergouvernementales canadiennes" => "Secrétariat aux affaires intergouvernementales canadiennes",
	"Secrétariat aux aînés" => "Secrétariat aux aînés",
	"Secrétariat de l'Ordre national du Québec" => "Secrétariat de l'Ordre national du Québec",
	"Secrétariat du Conseil du trésor" => "Secrétariat du Conseil du trésor",
	"Secrétariat du travail" => "Secrétariat du travail",
	"Société de développement de la Baie-James" => "Société de développement de la Baie-James",
	"Société de développement des entreprises culturelles" => "Société de développement des entreprises culturelles",
	"Société de financement des infrastructures locales du Québec" => "Société de financement des infrastructures locales du Québec",
	"Société de la Place des Arts" => "Société de la Place des Arts",
	"Société de l'assurance automobile du Québec" => "Société de l'assurance automobile du Québec",
	"Société de télédiffusion du Québec (Télé-Québec)" => "Société de télédiffusion du Québec (Télé-Québec)",
	"Société des alcools du Québec" => "Société des alcools du Québec",
	"Société des établissements de plein air du Québec" => "Société des établissements de plein air du Québec",
	"Société des loteries du Québec (Loto-Québec)" => "Société des loteries du Québec (Loto-Québec)",
	"Société des traversiers du Québec" => "Société des traversiers du Québec",
	"Société d'habitation du Québec" => "Société d'habitation du Québec",
	"Société du Centre des congrès de Québec" => "Société du Centre des congrès de Québec",
	"Société du Grand Théâtre de Québec" => "Société du Grand Théâtre de Québec",
	"Société du Palais des congrès de Montréal" => "Société du Palais des congrès de Montréal",
	"Société du parc industriel et portuaire de Bécancour" => "Société du parc industriel et portuaire de Bécancour",
	"Société du Plan Nord" => "Société du Plan Nord",
	"Société québécoise des infrastructures" => "Société québécoise des infrastructures",
	"Société québécoise d'information juridique" => "Société québécoise d'information juridique",
	"Tribunal administratif des marchés financiers" => "Tribunal administratif des marchés financiers",
	"Tribunal administratif du Québec" => "Tribunal administratif du Québec",
	"Tribunal administratif du travail" => "Tribunal administratif du travail",
	"Tribunal des droits de la personne" => "Tribunal des droits de la personne",
	"Vérificateur général du Québec" => "Vérificateur général du Québec");

	// default to invalid value, so it encourages users to select
	$quebec_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'quebec-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:ministry')), $quebec_ministries),
	));
?>

				<!-- Quebec -->
				<div class="form-group ministry-choices" id="quebec" hidden>
					<label for="quebec-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $quebec_choices ?>
				</div>

<?php
	$saskatchewan_ministries = array("Advanced Education" => "Advanced Education",
	"Agriculture" => "Agriculture",
	"Central Services" => "Central Services",
	"Economy" => "Economy",
	"Education" => "Education",
	"Energy and Resources" => "Energy and Resources",
	"Environment" => "Environment",
	"Finance" => "Finance",
	"Government Relations" => "Government Relations",
	"Health" => "Health",
	"Highways and Infrastructure" => "Highways and Infrastructure",
	"Justice" => "Justice",
	"Labour Relations and Workplace Safety" => "Labour Relations and Workplace Safety",
	"Parks, Culture and Sport" => "Parks, Culture and Sport",
	"Social Services" => "Social Services");

	// default to invalid value, so it encourages users to select
	$saskatchewan_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'saskatchewan-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $saskatchewan_ministries),
	));
?>

				<!-- Saskatchewan -->
				<div class="form-group ministry-choices" id="saskatchewan" hidden>
					<label for="saskatchewan-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $saskatchewan_choices ?>
				</div>

<?php
	$yukon_ministries = array("Community Services" => "Community Services",
	"Economic Development" => "Economic Development",
	"Education" => "Education",
	"Energy, Mines and Resources" => "Energy, Mines and Resources",
	"Environment" => "Environment",
	"Executive Council Office" => "Executive Council Office",
	"Finance" => "Finance",
	"French Language Services Directorate" => "French Language Services Directorate",
	"Health and Social Services" => "Health and Social Services",
	"Highways and Public Works" => "Highways and Public Works",
	"Justice" => "Justice",
	"Public Service Commission" => "Public Service Commission",
	"Tourism and Culture" => "Tourism and Culture",
	"Women's Directorate" => "Women's Directorate");

	// default to invalid value, so it encourages users to select
	$yukon_choices = elgg_view('input/select', array(
		'name' => 'ministry',
		'id' => 'yukon-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $yukon_ministries),
	));
?>

				<!-- Yukon -->
				<div class="form-group ministry-choices" id="yukon" hidden>
					<label for="yukon-choices" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:ministry'); ?></span></label>
					<?php echo $yukon_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("municipal")): ?>

<?php
	$municipal_governments = array();
	if (get_current_language() == 'en'){
		$municipal_governments = array("one", "two", "three");
	} else {
		$municipal_governments = array("un", "deux", "trois");
	}

	// default to invalid value, so it encourages users to select
	$municipal_choices = elgg_view('input/select', array(
		'name' => 'municipal',
		'id' => 'municipal-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $municipal_governments),
	));
?>

				<div class="form-group occupation-choices" id="municipal" hidden>
					<label for="municipal-choices" class="required"><span class="field-name"><?php echo elgg_echo('Municipal'); ?></span></label>
					<?php echo $municipal_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("international")): ?>

<?php
	$international_governments = array();
	if (get_current_language() == 'en'){
		$international_governments = array("one", "two", "three");
	} else {
		$international_governments = array("un", "deux", "trois");
	}

	// default to invalid value, so it encourages users to select
	$international_choices = elgg_view('input/select', array(
		'name' => 'international',
		'id' => 'international-choices',
        'class' => 'form-control',
		'options_values' => array_merge(array('default_invalid_value' => elgg_echo('gcRegister:make_selection')), $international_governments),
	));
?>

				<div class="form-group occupation-choices" id="international" hidden>
					<label for="international-choices" class="required"><span class="field-name"><?php echo elgg_echo('International'); ?></span></label>
					<?php echo $international_choices ?>
				</div>

<?php endif; ?>

<?php if(show_field("community") || show_field("business") || show_field("media") || show_field("other")): ?>

<?php
	$custom_occupation = elgg_view('input/text', array(
		'name' => 'custom_occupation',
		'id' => 'custom_occupation',
        'class' => 'form-control',
	));
?>

				<div class="form-group occupation-choices" id="custom" hidden>
					<label for="custom_occupation" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:custom'); ?></span></label>
					<?php echo $custom_occupation ?>
				</div>

<?php endif; ?>
				
				<!-- Display Name -->
				<div class="form-group">
					<label for="name" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:display_name'); ?></span></label>
					<font id="name_error" color="red"></font>
<?php
			echo elgg_view('input/text', array(
				'name' => 'name',
				'id' => 'name',
		        'class' => 'form-control display_name',
				'value' => $name,
			));
?>
				</div>
		    	<div class="alert alert-info"><?php echo elgg_echo('gcRegister:display_name_notice'); ?></div>

				<!-- Email -->
				<div class="form-group">
					<label for="email" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:email'); ?></span></label>
	    			<font id="email_error" color="red"></font>
					<input id="email" class="form-control" type="text" value='<?php echo $email ?>' name="email" onBlur="" />

	    		<script>	
	        		$('#email').blur(function () {
	            		elgg.action( 'register/ajax', {
							data: {
								email: $('#email').val()
							},
							success: function (x) {
			    				if (x.output == "<?php echo '> ' . elgg_echo('gcRegister:email_in_use'); ?>") {
					                $('#email_error').html("<?php echo elgg_echo('registration:userexists'); ?>").removeClass('hidden');
			    				} else if (x.output == "<?php echo '> ' . elgg_echo('gcRegister:invalid_email'); ?>") {
					                $('#email_error').text("<?php echo elgg_echo('gcRegister:invalid_email'); ?>").removeClass('hidden');
			    				} else {
			    					$('#email_error').addClass('hidden');
			    				}
							},   
						});
	        		});

	        		$('#name').blur(function () {
	        			elgg.action( 'register/ajax', {
							data: {
								name: $('#name').val()
							},
							success: function (x) {
			    				$('.username_test').val(x.output);
							},   
						});
	        		});
	    		</script>

				</div> <!-- end form-group div -->
		    	<div class="return_message"></div>

				<!-- Username (auto-generate) -->
				<div class="form-group" style="display:none">
					<label for="username" class="required" ><span class="field-name"><?php echo elgg_echo('gcRegister:username'); ?></span> </label> 
				    <div class="already-registered-message mrgn-bttm-sm"><span class="label label-danger tags mrgn-bttm-sm"></span></div>
<?php
			echo elgg_view('input/text', array(
				'name' => 'username',
				'id' => 'username',
		        'class' => 'username_test form-control',
				// 'readonly' => 'readonly',
				'value' => $username,
			));
?>
				</div>

				<!-- Password -->
				<div class="form-group">
					<label for="password" class="required"><span class="field-name"><span class="field-name"><?php echo elgg_echo('gcRegister:password_initial'); ?></span> </label>
					<font id="password_initial_error" color="red"></font>
<?php
			echo elgg_view('input/password', array(
				'name' => 'password',
				'id' => 'password',
		        'class'=>'password_test form-control',
				'value' => $password,
			));
?>
				</div>

				<!-- Secondary Password -->
				<div class="form-group">
					<label for="password2" class="required"><span class="field-name"><?php echo elgg_echo('gcRegister:password_secondary'); ?></span> </label>
				    <font id="password_secondary_error" color="red"></font>
<?php
			echo elgg_view('input/password', array(
				'name' => 'password2',
				'value' => $password2,
				'id' => 'password2',
		        'class'=>'password2_test form-control',
			));
?>
				</div>

			    <div class="checkbox"> <label><input type="checkbox" value="1" name="toc2" id="toc2" /><?php echo elgg_echo('gcRegister:terms_and_conditions')?></label> </div>

<?php
			// view to extend to add more fields to the registration form
			echo elgg_view('register/extend', $vars);

			// Add captcha hook
			echo elgg_view('input/captcha', $vars);
			echo '<div class="elgg-foot">';
			echo elgg_view('input/hidden', array('name' => 'friend_guid', 'value' => $vars['friend_guid']));
			echo elgg_view('input/hidden', array('name' => 'invitecode', 'value' => $vars['invitecode']));

			// note: disable
			echo elgg_view('input/submit', array(
			    'name' => 'submit',
			    'value' => elgg_echo('gcRegister:register'),
			    'id' => 'submit',
			    'class'=>'submit_test btn-primary',));
			echo '</div>';
?>
	            
			</div>
		</div>
	</section>

<script>
	// check if the initial email input is empty, then proceed to validate email
    $('#email').on("focusout", function() {
    	var val = $(this).val();
        if ( val === '' ) {
        	var c_err_msg = '<?php echo elgg_echo('gcRegister:empty_field') ?>';
            document.getElementById('email_error').innerHTML = c_err_msg;
        } else if ( val !== '' ) {
            document.getElementById('email_error').innerHTML = '';
            
            if (!validateEmail(val)) {
            	var c_err_msg = '<?php echo elgg_echo('gcRegister:invalid_email') ?>';
            	document.getElementById('email_error').innerHTML = c_err_msg;
            }
        }
    });

    $('.password_test').on("focusout", function() {
    	var val = $(this).val();
	    if ( val === '' ) {
	    	var c_err_msg = "<?php echo elgg_echo('gcRegister:empty_field') ?>";
	        document.getElementById('password_initial_error').innerHTML = c_err_msg;
	    } else if ( val !== '' ) {
	        document.getElementById('password_initial_error').innerHTML = '';
	    }

        var val_2 = $('#password2').val();
        if (val_2 == val) {
	        document.getElementById('password_secondary_error').innerHTML = '';
        } else if (val_2 !== '' && val_2 != val) {
            var c_err_msg = "<?php echo elgg_echo('gcRegister:mismatch') ?>";
	        document.getElementById('password_secondary_error').innerHTML = c_err_msg;
        }
	});	
    
    $('#password2').on("focusout", function() {
    	var val = $(this).val();
	    if ( val === '' ) {
	    	var c_err_msg = "<?php echo elgg_echo('gcRegister:empty_field') ?>";
	        document.getElementById('password_secondary_error').innerHTML = c_err_msg;
	    } else if ( val !== '' ) {
	        document.getElementById('password_secondary_error').innerHTML = '';
	        
	        var val2 = $('.password_test').val();
	        if (val2 != val) {
	        	var c_err_msg = "<?php echo elgg_echo('gcRegister:mismatch') ?>";
	        	document.getElementById('password_secondary_error').innerHTML = c_err_msg;
	        }
	    }
	});
    
    $('#name').on("focusout", function() {
    	var val = $(this).val();
        if ( val === '' ) {
        	var c_err_msg = '<?php echo elgg_echo('gcRegister:empty_field') ?>';
            document.getElementById('name_error').innerHTML = c_err_msg;
        } else if ( val !== '' ) {
            document.getElementById('name_error').innerHTML = '';
        }
    });

    $("form.elgg-form-register").on("submit", function(){
	    $(".occupation-choices select:not(:visible), .occupation-choices input:not(:visible), .student-choices select:not(:visible), .ministry-choices select:not(:visible)").attr('disabled', 'disabled');
	    $(".occupation-choices select:visible, .occupation-choices input:visible, .student-choices select:visible, .ministry-choices select:visible").removeAttr('disabled');
	});
</script>

</div>
