<!-- General JS Files used across Cloudlog -->
<script src="<?php echo base_url(); ?>assets/js/jquery-3.3.1.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/popper.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.fancybox.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.jclock.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/leaflet/leaflet.js"></script>
<script type="text/javascript" src="<?php echo base_url() ;?>assets/js/radiohelpers.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrapdialog/js/bootstrap-dialog.min.js"></script>
<script type="text/javascript">
  /*
  *
  * Define global javascript variables
  *
  */
  var base_url = "<?php echo base_url(); ?>"; // Base URL
  var site_url = "<?php echo site_url(); ?>"; // Site URL
  var icon_dot_url = "<?php echo base_url();?>assets/images/dot.png";
</script>


<script>

function load_was_map() {
    BootstrapDialog.show({
            title: 'Worked All States Map ('+$('#band2').val()+')',
            cssClass: 'was-map-dialog',
            message: $('<div></div>').load(site_url + '/awards/was_map/' + $('#band2').val())
    });
}

</script>

<?php if ($this->uri->segment(1) == "adif") { ?>
    <!-- Javascript used for ADIF Import and Export Areas -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="<?php echo base_url() ;?>assets/js/sections/adif.js"></script>
<?php } ?>

<?php if ($this->uri->segment(1) == "notes" && ($this->uri->segment(2) == "add" || $this->uri->segment(2) == "edit") ) { ?>
    <!-- Javascript used for Notes Area -->
    <script src="<?php echo base_url() ;?>assets/plugins/quill/quill.min.js"></script>
    <script src="<?php echo base_url() ;?>assets/js/sections/notes.js"></script>
<?php } ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/datatables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.js"></script>

<?php if ($this->uri->segment(1) == "search" && $this->uri->segment(2) == "filter") { ?>

<script type="text/javascript" src="<?php echo base_url() ;?>assets/js/query-builder.standalone.min.js"></script>

<script type="text/javascript">

$(".search-results-box").hide();

    $('#builder').queryBuilder({
    filters: [
      <?php foreach ($get_table_names->result() as $row) {
        $value_name = str_replace("COL_", "", $row->Field);
        if ($value_name != "PRIMARY_KEY" && strpos($value_name, 'MY_') === false && strpos($value_name, '_INTL') == false) { ?>
        {
          id: '<?php echo $row->Field; ?>',
          label: '<?php echo $value_name; ?>',
          <?php if (strpos($row->Type, 'int(') !== false) { ?>
          type: 'integer',
          operators: ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal']
          <?php } elseif(strpos($row->Type, 'double') !== false) { ?>
          type: 'double',
          operators: ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal']
          <?php } elseif(strpos($row->Type, 'datetime') !== false) { ?>
          type: 'datetime',
          operators: ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal']
          <?php } else { ?>
          type: 'string',
          operators: ['equal', 'not_equal', 'begins_with', 'contains', 'ends_with', 'is_empty', 'is_not_empty', 'is_null', 'is_not_null']
          <?php } ?>
        },
        <?php } ?>
      <?php } ?>
    ]
  });

  $('#btn-get').on('click', function() {
    var result = $('#builder').queryBuilder('getRules');
    if (!$.isEmptyObject(result)) {
      //alert(JSON.stringify(result, null, 2));

      $.post( "<?php echo site_url('search/json_result');?>", { search: JSON.stringify(result, null, 2), temp: "testvar" })
      .done(function( data ) {
        //console.log(data)
        //alert( "Data Loaded: " + data );
        $('.qso').remove();
        $(".search-results-box").show();

        $.each(JSON.parse(data), function(i, item) {

          var band = "";
          if(item.COL_SAT_NAME != "") {
            band = item.COL_SAT_NAME;
          } else {
            band = item.COL_BAND;
          }
          var callsign = '<a href="javascript:displayQso(' + item.COL_PRIMARY_KEY + ');" >' + item.COL_CALL + '</a>';
          if (item.COL_SUBMODE == '' || item.COL_SUBMODE == null) {
            $('#results').append('<tr class="qso"><td>' + item.COL_TIME_ON + '</td><td>' + callsign + '</td><td>' + item.COL_MODE + '</td><td>' + item.COL_RST_SENT + '</td><td>' + item.COL_RST_RCVD + '</td><td>' + band + '</td><td>' + item.COL_COUNTRY + '</td><td></td></tr>');
          }
          else {
            $('#results').append('<tr class="qso"><td>' + item.COL_TIME_ON + '</td><td>' + callsign + '</td><td>' + item.COL_SUBMODE + '</td><td>' + item.COL_RST_SENT + '</td><td>' + item.COL_RST_RCVD + '</td><td>' + band + '</td><td>' + item.COL_COUNTRY + '</td><td></td></tr>');
          }
        });

      });
    }
    else{
      //console.log("invalid object :");
    }
  });

  $('#btn-set').on('click', function() {
    //$('#builder').queryBuilder('setRules', rules_basic);
    var result = $('#builder').queryBuilder('getRules');
    if (!$.isEmptyObject(result)) {
      rules_basic = result;
    }
  });

  //When rules changed :
  $('#builder').on('getRules.queryBuilder.filter', function(e) {
    //$log.info(e.value);
  });
</script>
<?php } ?>

<script>
$(document).ready(function() {
	$('#create_station_profile #country').val($("#dxcc_select option:selected").text());
	$("#create_station_profile #dxcc_select" ).change(function() {
	  $('#country').val($("#dxcc_select option:selected").text());
	});
});
</script>

<script>
var $= jQuery.noConflict();
$('[data-fancybox]').fancybox({
    toolbar  : false,
    smallBtn : true,
    iframe : {
        preload : false
    }
});

// Here we capture ALT-L to invoice the Quick lookup
document.onkeyup = function(e) {
	// ALT-W wipe
	if (e.altKey && e.which == 76) {
		spawnLookupModal();
	}
};

// This displays the dialog with the form and it's where the resulttable is displayed
function spawnLookupModal() {
	$.ajax({
		url: base_url + 'index.php/lookup',
		type: 'post',
		success: function (html) {
			BootstrapDialog.show({
				title: 'Quick lookup',
				size: BootstrapDialog.SIZE_WIDE,
				cssClass: 'lookup-dialog',
				nl2br: false,
				message: html,
				onshown: function(dialog) {
					$('#quicklookuptype').change(function(){
						var type = $('#quicklookuptype').val();
						if (type == "dxcc") {
							$('#quicklookupdxcc').show();
							$('#quicklookupiota').hide();
							$('#quicklookupcqz').hide();
							$('#quicklookupwas').hide();
							$('#quicklookuptext').hide();
						} else if (type == "iota") {
							$('#quicklookupiota').show();
							$('#quicklookupdxcc').hide();
							$('#quicklookupcqz').hide();
							$('#quicklookupwas').hide();
							$('#quicklookuptext').hide();
						} else if (type == "grid" || type == "sota" || type == "wwff") {
							$('#quicklookuptext').show();
							$('#quicklookupiota').hide();
							$('#quicklookupdxcc').hide();
							$('#quicklookupcqz').hide();
							$('#quicklookupwas').hide();
						} else if (type == "cqz") {
							$('#quicklookupcqz').show();
							$('#quicklookupiota').hide();
							$('#quicklookupdxcc').hide();
							$('#quicklookupwas').hide();
							$('#quicklookuptext').hide();
						} else if (type == "was") {
							$('#quicklookupwas').show();
							$('#quicklookupcqz').hide();
							$('#quicklookupiota').hide();
							$('#quicklookupdxcc').hide();
							$('#quicklookuptext').hide();
						}
					});
				},
				buttons: [{
					label: 'Close',
					action: function (dialogItself) {
						dialogItself.close();
					}
				}]
			});
		}
	});
}

// This function executes the call to the backend for fetching queryresult and displays the table in the dialog
function getLookupResult() {
	$(".ld-ext-right").addClass('running');
	$(".ld-ext-right").prop('disabled', true);
	$.ajax({
		url: base_url + 'index.php/lookup/search',
		type: 'post',
		data: {
			type: $('#quicklookuptype').val(),
			dxcc: $('#quicklookupdxcc').val(),
			was:  $('#quicklookupwas').val(),
			grid: $('#quicklookuptext').val(),
			cqz:  $('#quicklookupcqz').val(),
			iota: $('#quicklookupiota').val(),
			sota: $('#quicklookuptext').val(),
			wwff: $('#quicklookuptext').val(),
		},
		success: function (html) {
			$('#lookupresulttable').html(html);
			$(".ld-ext-right").removeClass('running');
			$(".ld-ext-right").prop('disabled', false);
		}
	});
}

</script>

<?php if ($this->uri->segment(1) == "map" && $this->uri->segment(2) == "custom") { ?>
<!-- Javascript used for ADIF Import and Export Areas -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/L.Maidenhead.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/leafembed.js"></script>
    <script type="text/javascript">
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      });

        <?php if($qra == "set") { ?>
        var q_lat = <?php echo $qra_lat; ?>;
        var q_lng = <?php echo $qra_lng; ?>;
        <?php } else { ?>
        var q_lat = 40.313043;
        var q_lng = -32.695312;
        <?php } ?>

        var qso_loc = '<?php echo site_url('map/map_data_custom/');?><?php echo rawurlencode($date_from); ?>/<?php echo rawurlencode($date_to); ?>/<?php echo rawurlencode($this->input->post('band')); ?>';
        var q_zoom = 2;

      $(document).ready(function(){
            <?php if ($this->config->item('map_gridsquares') != FALSE) { ?>
              var grid = "Yes";
            <?php } else { ?>
              var grid = "No";
            <?php } ?>
            initmap(grid);

      });
    </script>
<?php } ?>

<?php if ($this->uri->segment(1) == "map" && $this->uri->segment(2) == "") { ?>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/L.Maidenhead.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/leafembed.js"></script>
    <script type="text/javascript">
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      });

        <?php if($qra == "set") { ?>
        var q_lat = <?php echo $qra_lat; ?>;
        var q_lng = <?php echo $qra_lng; ?>;
        <?php } else { ?>
        var q_lat = 40.313043;
        var q_lng = -32.695312;
        <?php } ?>

        var qso_loc = '<?php echo site_url('map/map_data');?>';
        var q_zoom = 2;

      $(document).ready(function(){
            <?php if ($this->config->item('map_gridsquares') != FALSE) { ?>
              var grid = "Yes";
            <?php } else { ?>
              var grid = "No";
            <?php } ?>
            initmap(grid);

      });
    </script>
<?php } ?>

<?php if ($this->uri->segment(1) == "" || $this->uri->segment(1) == "dashboard" ) { ?>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/L.Maidenhead.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/leafembed.js"></script>
    <script type="text/javascript">
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      });

        <?php if($qra == "set") { ?>
        var q_lat = <?php echo $qra_lat; ?>;
        var q_lng = <?php echo $qra_lng; ?>;
        <?php } else { ?>
        var q_lat = 40.313043;
        var q_lng = -32.695312;
        <?php } ?>

        var qso_loc = '<?php echo site_url('dashboard/map');?>';
        var q_zoom = 3;

      $(document).ready(function(){
            <?php if ($this->config->item('map_gridsquares') != FALSE) { ?>
              var grid = "Yes";
            <?php } else { ?>
              var grid = "No";
            <?php } ?>
            initmap(grid);

      });
    </script>
<?php } ?>



<?php if ($this->uri->segment(1) == "radio") { ?>
<!-- If this is the admin/radio page run the JS -->
<script type="text/javascript">
    $(document).ready(function(){
        setInterval(function() {
            // Get Mode
            $.get('radio/status/', function(result) {
                    //$('.status').append(result);
                    $('.status').html(result);
            });
        }, 2000);
 });
</script>
<?php } ?>


<?php if ($this->uri->segment(1) == "search") { ?>
<script type="text/javascript">
i=0;

function searchButtonPress(){
    event.preventDefault()
    if ($('#callsign').val()) {
      $('#partial_view').load("logbook/search_result/" + $('#callsign').val(), function() {});
    }
}

$(document).ready(function(){

  <?php if($this->input->post('callsign') != "") { ?>
    $('#partial_view').load("logbook/search_result/<?php echo $this->input->post('callsign'); ?>", function() {
    });
  <?php } ?>

$(document).on('keypress',function(e) {
  if(e.which == 13) {

    if ($('#callsign').val()) {
      $('#partial_view').load("logbook/search_result/" + $('#callsign').val(), function() {});
    }


     event.preventDefault();
        return false;
  }
});


});
</script>
<?php } ?>

<?php if ($this->uri->segment(1) == "logbook" && $this->uri->segment(2) != "view") { ?>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/L.Maidenhead.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/leafembed.js"></script>
    <script type="text/javascript">
        <?php if($qra == "set") { ?>
        var q_lat = <?php echo $qra_lat; ?>;
        var q_lng = <?php echo $qra_lng; ?>;
        <?php } else { ?>
        var q_lat = 40.313043;
        var q_lng = -32.695312;
        <?php } ?>

        var qso_loc = '<?php echo site_url('logbook/qso_map/25/'.$this->uri->segment(3)); ?>';
        var q_zoom = 3;

        <?php if ($this->config->item('map_gridsquares') != FALSE) { ?>
              var grid = "Yes";
        <?php } else { ?>
              var grid = "No";
        <?php } ?>
            initmap(grid);

    </script>
<?php } ?>

<?php if ($this->uri->segment(1) == "qso") { ?>
<script src="<?php echo base_url() ;?>assets/js/sections/qso.js"></script>

<script>
  var markers = L.layerGroup();
  var mymap = L.map('qsomap').setView([51.505, -0.09], 13);

  L.tileLayer('<?php echo $this->optionslib->get_option('map_tile_server');?>', {
    maxZoom: 18,
    attribution: '<?php echo $this->optionslib->get_option('map_tile_server_copyright');?>',
    id: 'mapbox.streets'
  }).addTo(mymap);

</script>

  <script type="text/javascript">

    var manual = <?php echo $_GET['manual']; ?>;

    $(document).ready(function() {

    $('.callsign-suggest').hide();

    setRst($(".mode").val());

    /* On Page Load */
    var catcher = function() {
      var changed = false;
      $('form').each(function() {
        if ($(this).data('initialForm') != $(this).serialize()) {
          changed = true;
          $(this).addClass('changed');
        } else {
          $(this).removeClass('changed');
        }
      });
      if (changed) {
        return 'Unsaved QSO!';
      }
    };

    $(function() {
      $('form').each(function() {
        $(this).data('initialForm', $(this).serialize());
      }).submit(function(e) {
        var formEl = this;
        var changed = false;
        $('form').each(function() {
          if (this != formEl && $(this).data('initialForm') != $(this).serialize()) {
            changed = true;
            $(this).addClass('changed');
          } else {
            $(this).removeClass('changed');
          }
        });
        if (changed && !confirm('You have an unsaved QSO. Continue with QSO?')) {
          e.preventDefault();
        } else {
          $(window).unbind('beforeunload', catcher);
        }
      });
      $(window).bind('beforeunload', catcher);
    });

     // Callsign always has focus on load
      $("#callsign").focus();

      if ( ! manual ) {
        $(function($) {
          var options = {
            utc: true,
            format: '%H:%M:%S'
          }
          $('.input_time').jclock(options);
        });

        $(function($) {
          var options = {
            utc: true,
            format: '%d-%m-%Y'
          }
          $('.input_date').jclock(options);
        });
      }
    });



  jQuery(function($) {
  var input = $('#callsign');
  input.on('keydown', function() {
    var key = event.keyCode || event.charCode;

    if( key == 8 || key == 46 ) {
      reset_fields();
    }
  });

  $(document).keyup(function(e) {
     if (e.key === "Escape") { // escape key maps to keycode `27`
       reset_fields();
	   $('#callsign').val("");
	   $("#callsign").focus();
    }
  });
});

<?php if ($this->session->userdata('user_sota_lookup') == 1) { ?>
	$('#sota_ref').change(function() {
		var sota = $('#sota_ref').val();
		if (sota.length > 0) {
			$.ajax({
				url: base_url+'index.php/qso/get_sota_info',
				type: 'post',
				data: {'sota': sota},
				success: function(res) {
					$('#qth').val(res.name);
					$('#locator').val(res.locator);
				},
				error: function() {
					$('#qth').val('');
					$('#locator').val('');
				},
			});
		}
	});
<?php } ?>

<?php if ($this->config->item('qso_auto_qth')) { ?>
    $('#qth').focusout(function() {
    	if ($('#locator').val() === '') {
			var lat = 0;
			var lon = 0;
			$.ajax({
				async: false,
				type: 'GET',
				dataType: "json",
				url: "https://nominatim.openstreetmap.org/search/?city=" + $(this).val() + "&format=json&addressdetails=1&limit=1",
				data: {},
				success: function (data) {
					if (typeof data[0].lat !== 'undefined') {
						lat = parseFloat(data[0].lat);
					}
					if (typeof data[0].lon !== 'undefined') {
						lon = parseFloat(data[0].lon);
					}
				},
			});
			if (lat !== 0 && lon !== 0) {
				var qthloc = LatLng2Loc(lat, lon, 10);
				if (qthloc.length > 0) {
					$('#locator').val(qthloc.substr(0, 6)).trigger('focusout');
				}
			}
		}
	});

	LatLng2Loc = function(y, x, num) {
		if (x < -180) {
			x = x + 360;
		}
		if (x > 180) {
			x = x - 360;
		}
		var yqth, yi, yk, ydiv, yres, ylp, y;
		var ycalc = new Array(0, 0, 0);
		var yn = new Array(0, 0, 0, 0, 0, 0, 0);

		var ydiv_arr = new Array(10, 1, 1 / 24, 1 / 240, 1 / 240 / 24);
		ycalc[0] = (x + 180) / 2;
		ycalc[1] = y + 90;

		for (yi = 0; yi < 2; yi++) {
			for (yk = 0; yk < 5; yk++) {
				ydiv = ydiv_arr[yk];
				yres = ycalc[yi] / ydiv;
				ycalc[yi] = yres;
				if (ycalc[yi] > 0) ylp = Math.floor(yres); else ylp = Math.ceil(yres);
				ycalc[yi] = (ycalc[yi] - ylp) * ydiv;
				yn[2 * yk + yi] = ylp;
			}
		}

		var qthloc = "";
		if (num >= 2) qthloc += String.fromCharCode(yn[0] + 0x41) + String.fromCharCode(yn[1] + 0x41);
		if (num >= 4) qthloc += String.fromCharCode(yn[2] + 0x30) + String.fromCharCode(yn[3] + 0x30);
		if (num >= 6) qthloc += String.fromCharCode(yn[4] + 0x41) + String.fromCharCode(yn[5] + 0x41);
		if (num >= 8) qthloc += ' ' + String.fromCharCode(yn[6] + 0x30) + String.fromCharCode(yn[7] + 0x30);
		if (num >= 10) qthloc += String.fromCharCode(yn[8] + 0x61) + String.fromCharCode(yn[9] + 0x61);
		return qthloc;
	}
	<?php } ?>

  </script>

<?php } ?>
<?php if ( ($this->uri->segment(1) == "qso" && $_GET['manual'] == 0) || $this->uri->segment(1) == "contesting") { ?>
    <script>
    function setRst(mode) {
        if(mode == 'JT65' || mode == 'JT65B' || mode == 'JT6C' || mode == 'JTMS' || mode == 'ISCAT' || mode == 'MSK144' || mode == 'JTMSK' || mode == 'QRA64' || mode == 'FT8' || mode == 'FT4' || mode == 'JS8' || mode == 'JT9' || mode == 'JT9-1' || mode == 'ROS'){
            $('#rst_sent').val('-5');
            $('#rst_recv').val('-5');
        } else if (mode == 'FSK441' || mode == 'JT6M') {
            $('#rst_sent').val('26');
            $('#rst_recv').val('26');
        } else if (mode == 'CW' || mode == 'RTTY' || mode == 'PSK31' || mode == 'PSK63') {
            $('#rst_sent').val('599');
            $('#rst_recv').val('599');
        } else {
            $('#rst_sent').val('59');
            $('#rst_recv').val('59');
        }
    }
    </script>
<?php } ?>
<?php if ( ($this->uri->segment(1) == "qso" && $_GET['manual'] == 0) || $this->uri->segment(1) == "contesting") { ?>
    <script>
        // Javascript for controlling rig frequency.
  var updateFromCAT = function() {
    if($('select.radios option:selected').val() != '0') {
      radioID = $('select.radios option:selected').val();
      $.getJSON( "radio/json/" + radioID, function( data ) {
          /* {
              "uplink_freq": "2400210000",
              "downlink_freq": "10489710000",
              "mode": "SSB",
              "satmode": "",
              "satname": "ES'HAIL-2"
          }  */
          if (data.uplink_freq != "")
          {
            $('#frequency').val(data.uplink_freq);
            $("#band").val(frequencyToBand(data.uplink_freq));
          }
          if (data.downlink_freq != "")
          {
            $('#frequency_rx').val(data.downlink_freq);
            $("#band_rx").val(frequencyToBand(data.downlink_freq));
          }

          old_mode = $(".mode").val();
          if (data.mode == "LSB" || data.mode == "USB" || data.mode == "SSB") {
            $(".mode").val('SSB');
          } else {
            $(".mode").val(data.mode);
          }

          if (old_mode !== $(".mode").val()) {
            // Update RST on mode change via CAT
            setRst($(".mode").val());
          }
          $("#sat_name").val(data.satname);
          $("#sat_mode").val(data.satmode);

          // Display CAT Timeout warnng based on the figure given in the config file
            var minutes = Math.floor(<?php echo $this->config->item('cat_timeout_interval'); ?> / 60);

            if(data.updated_minutes_ago > minutes) {
              if($('.radio_timeout_error').length == 0) {
                $('.qso_panel').prepend('<div class="alert alert-danger radio_timeout_error" role="alert">Radio connection timed-out: ' + $('select.radios option:selected').text() + ' data is ' + data.updated_minutes_ago + ' minutes old.</div>');
              } else {
                $('.radio_timeout_error').text('Radio connection timed-out: ' + $('select.radios option:selected').text() + ' data is ' + data.updated_minutes_ago + ' minutes old.');
              }
            } else {
              $(".radio_timeout_error" ).remove();
            }

      });
    }
  };

  // Update frequency every three second
  setInterval(updateFromCAT, 3000);

  // If a radios selected from drop down select radio update.
  $('.radios').change(updateFromCAT);

  // If radio isn't SatPC32 clear sat_name and sat_mode
  $( ".radios" ).change(function() {
      if ($(".radios option:selected").text() != "SatPC32") {
        $("#sat_name").val("");
        $("#sat_mode").val("");
        $("#frequency").val("");
        $("#frequency_rx").val("");
        $("#band_rx").val("");
        $("#selectPropagation").val($("#selectPropagation option:first").val());
      }

      if ($(".radios option:selected").text() == "None") {
        $(".radio_timeout_error" ).remove();
      }

  });
  </script>

<?php } ?>

<?php if ($this->uri->segment(1) == "logbook" && $this->uri->segment(2) == "view") { ?>
<script>

  var mymap = L.map('map').setView([lat,long], 5);

  L.tileLayer('<?php echo $this->optionslib->get_option('map_tile_server');?>', {
    maxZoom: 18,
    attribution: '<?php echo $this->optionslib->get_option('map_tile_server_copyright');?>',
    id: 'mapbox.streets'
  }).addTo(mymap);


  var redIcon = L.icon({
      iconUrl: icon_dot_url,
      iconSize:     [18, 18], // size of the icon
  });

  L.marker([lat,long], {icon: redIcon}).addTo(mymap)
    .bindPopup(callsign);

  mymap.on('click', onMapClick);

</script>
<?php } ?>

<?php if ($this->uri->segment(1) == "update") { ?>
<script>
$(document).ready(function(){
    $('#btn_update_dxcc').bind('click', function(){
        $('#dxcc_update_status').show();
        $.ajax({url:"update/dxcc"});
        setTimeout(update_stats,5000);
    });
    function update_stats(){
        $('#dxcc_update_status').load('<?php echo base_url()?>updates/status.html', function(val){
            $('#dxcc_update_staus').html(val);

            if ((val  === null) || (val.substring(0,4) !="DONE")){
                setTimeout(update_stats, 5000);
            }
        });

    }

});
</script>

<?php } ?>

<?php if ($this->uri->segment(1) == "gridsquares") { ?>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/leaflet/L.MaidenheadColoured.js"></script>

<script>

  var layer = L.tileLayer('<?php echo $this->optionslib->get_option('map_tile_server');?>', {
    maxZoom: 18,
    attribution: '<?php echo $this->optionslib->get_option('map_tile_server_copyright');?>',
    id: 'mapbox.streets'
  });


  var map = L.map('map', {
    layers: [layer],
    center: [19, 0],
    zoom: 3
  });

  var grid_two = <?php echo $grid_2char; ?>;
  var grid_four = <?php echo $grid_4char; ?>;
  var grid_six = <?php echo $grid_6char; ?>;

  var grid_two_confirmed = <?php echo $grid_2char_confirmed; ?>;
  var grid_four_confirmed = <?php echo $grid_4char_confirmed; ?>;
  var grid_six_confirmed = <?php echo $grid_6char_confirmed; ?>;

  var maidenhead = L.maidenhead().addTo(map);

  map.on('click', onMapClick);

  function onMapClick(event) {
    var LatLng = event.latlng;
    var lat = LatLng.lat;
    var lng = LatLng.lng;
    var locator = LatLng2Loc(lat,lng, 10);
    var loc_4char = locator.substring(0, 4);
    console.log(loc_4char);
    console.log(map.getZoom());

    if(map.getZoom() > 5) {
      var search_type = "<?php echo $this->uri->segment(2); ?>";
      if(search_type == "satellites") {
        console.log("satellites search");
        var search_tags = "search_sat/" + loc_4char;
      } else {
        var band = "<?php echo $this->uri->segment(3); ?>";
        console.log(band);
        var search_tags = "search_band/" + band + "/" + loc_4char;
      }

      $.getJSON( "<?php echo site_url('gridsquares/');?>" + search_tags, function( data ) {
        var count = Object.keys(data).length;
        console.log("Count: " + count);
        var items = [];
        $.each( data, function( i, item ) {
          console.log(item.COL_CALL + item.COL_SAT_NAME);
          if(item.COL_SAT_NAME != undefined) {
            items.push( "<tr><td>" + item.COL_TIME_ON + "</td><td>" + item.COL_CALL + "</td><td>" + item.COL_MODE + "</td><td>" + item.COL_SAT_NAME + "</td><td>" + item.COL_GRIDSQUARE + item.COL_VUCC_GRIDS + "</td></tr>" );
          } else {
            items.push( "<tr><td>" + item.COL_TIME_ON + "</td><td>" + item.COL_CALL + "</td><td>" + item.COL_MODE + "</td><td>" + item.COL_BAND + "</td><td>" + item.COL_GRIDSQUARE + item.COL_VUCC_GRIDS + "</td></tr>" );
          }
        });

        $('#qso_count').text(count);
        if (count > 1) {
           $('#gt1_qso').text("s");
        } else {
           $('#gt1_qso').text("");
        }

        $("#grid_results tbody").empty();
        if (count > 0) {
          $("#grid_results tbody").append(items.join( "" ));

          $('#square_number').text(loc_4char);
          $('#exampleModal').modal('show');
        }

      });
    }
  };

<?php if ($this->uri->segment(1) == "gridsquares" && $this->uri->segment(2) == "band") { ?>

  var bands_available = <?php echo $bands_available; ?>;
  $('#gridsquare_bands').append('<option value="All">All</option>')
  $.each(bands_available, function(key, value) {
     $('#gridsquare_bands')
         .append($("<option></option>")
                    .attr("value",value)
                    .text(value));
  });

  var num = "<?php echo $this->uri->segment(3);?>";
    $("#gridsquare_bands option").each(function(){
        if($(this).val()==num){ // EDITED THIS LINE
            $(this).attr("selected","selected");
        }
    });

  $(function(){
      // bind change event to select
      $('#gridsquare_bands').on('change', function () {
          var url = $(this).val(); // get selected value
          if (url) { // require a URL
              window.location = "<?php echo site_url('gridsquares/band/');?>" + url
          }
          return false;
      });
    });
<?php } ?>

</script>
<?php } ?>

<?php if ($this->uri->segment(1) == "dayswithqso") { ?>
    <script src="<?php echo base_url(); ?>assets/js/chart.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/sections/dayswithqso.js"></script>
<?php } ?>

<?php if ($this->uri->segment(1) == "distances") { ?>
    <script src="<?php echo base_url(); ?>assets/js/highstock.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/highstock/exporting.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/highstock/offline-exporting.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/highstock/export-data.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/sections/distances.js"></script>
<?php } ?>

    <?php if ($this->uri->segment(2) == "import") { ?>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    format: 'DD/MM/YYYY',
                });
            });
        </script>
    <?php } ?>

    <?php if ($this->uri->segment(1) == "qrz") { ?>
		<script src="<?php echo base_url(); ?>assets/js/sections/qrzlogbook.js"></script>
    <?php } ?>

	<script>
		function displayQso(id) {
			var baseURL= "<?php echo base_url();?>";
			$.ajax({
				url: baseURL + 'index.php/logbook/view/' + id,
				type: 'post',
				success: function(html) {
					BootstrapDialog.show({
						title: 'QSO Data',
						cssClass: 'qso-dialog',
						size: BootstrapDialog.SIZE_WIDE,
						nl2br: false,
						message: html,
						onshown: function(dialog) {
							var qsoid = $("#qsoid").text();
							$(".editButton").html('<a class="btn btn-primary" id="edit_qso" href="javascript:qso_edit('+qsoid+')"><i class="fas fa-edit"></i> Edit QSO</a>');
							var lat = $("#lat").text();
							var long = $("#long").text();
							var callsign = $("#callsign").text();
							var mymap = L.map('mapqso').setView([lat,long], 5);

							L.tileLayer('<?php echo $this->optionslib->get_option('map_tile_server');?>', {
								maxZoom: 18,
								attribution: '<?php echo $this->optionslib->get_option('map_tile_server_copyright');?>',
								id: 'mapbox.streets'
							}).addTo(mymap);

							var redIcon = L.icon({
								iconUrl: icon_dot_url,
								iconSize:     [18, 18], // size of the icon
							});

							L.marker([lat,long], {icon: redIcon}).addTo(mymap)
								.bindPopup(callsign);

						},
					});

				}
			});
		}
		</script>


<?php if ($this->uri->segment(2) == "dxcc") { ?>
<script>
    $('.tabledxcc').DataTable({
        "pageLength": 25,
        responsive: false,
        ordering: false,
        "scrollY":        "400px",
        "scrollCollapse": true,
        "paging":         false,
        "scrollX": true,
        dom: 'Bfrtip',
        buttons: [
            'csv'
        ]
    });

    $('.tablesummary').DataTable({
        info: false,
        searching: false,
        ordering: false,
        "paging":         false,
        dom: 'Bfrtip',
        buttons: [
            'csv'
        ]
    });

    // using this to change color of csv-button if dark mode is chosen
    var background = $('body').css( "background-color");

    if (background != ('rgb(255, 255, 255)')) {
        $(".buttons-csv").css("color", "white");
    }
 </script>
    <?php } ?>

<?php if ($this->uri->segment(2) == "vucc_band") { ?>
    <script>
    $('.tablevucc').DataTable({
        "pageLength": 25,
        responsive: false,
        ordering: false,
        "scrollY":        "400px",
        "scrollCollapse": true,
        "paging":         false,
        "scrollX": true,
        dom: 'Bfrtip',
        buttons: [
            'csv'
        ]
    });

    // using this to change color of csv-button if dark mode is chosen
    var background = $('body').css( "background-color");

    if (background != ('rgb(255, 255, 255)')) {
        $(".buttons-csv").css("color", "white");
    }
    </script>
<?php } ?>


<?php if ($this->uri->segment(2) == "dok") { ?>
    <script>
        function displayDokContacts(dok, band) {
            var baseURL= "<?php echo base_url();?>";
            $.ajax({
                url: baseURL + 'index.php/awards/dok_details_ajax',
                type: 'post',
                data: {'Dok': dok,
                    'Band': band
                },
                success: function(html) {
                    BootstrapDialog.show({
                        title: 'QSO Data',
                        size: BootstrapDialog.SIZE_WIDE,
                        cssClass: 'qso-dok-dialog',
                        nl2br: false,
                        message: html,
                        buttons: [{
                            label: 'Close',
                            action: function (dialogItself) {
                                dialogItself.close();
                            }
                        }]
                    });
                }
            });
        }
    </script>
<?php } ?>

<?php if ($this->uri->segment(2) == "iota") { ?>
    <script>

        $('.tableiota').DataTable({
            "pageLength": 25,
            responsive: false,
            ordering: false,
            "scrollY":        "400px",
            "scrollCollapse": true,
            "paging":         false,
            "scrollX": true,
            dom: 'Bfrtip',
            buttons: [
                'csv'
            ]
        });

        $('.tablesummary').DataTable({
            info: false,
            searching: false,
            ordering: false,
            "paging":         false,
            dom: 'Bfrtip',
            buttons: [
                'csv'
            ]
        });

        // using this to change color of csv-button if dark mode is chosen
        var background = $('body').css( "background-color");

        if (background != ('rgb(255, 255, 255)')) {
            $(".buttons-csv").css("color", "white");
        }
    </script>

<?php } ?>

<?php if ($this->uri->segment(2) == "cq") { ?>
    <script>
        $('.tablecq').DataTable({
            "pageLength": 25,
            responsive: false,
            ordering: false,
            "scrollY":        "400px",
            "scrollCollapse": true,
            "paging":         false,
            "scrollX": true,
            dom: 'Bfrtip',
            buttons: [
                'csv'
            ]
        });

        $('.tablesummary').DataTable({
            info: false,
            searching: false,
            ordering: false,
            "paging":         false,
            dom: 'Bfrtip',
            buttons: [
                'csv'
            ]
        });

        // using this to change color of csv-button if dark mode is chosen
        var background = $('body').css( "background-color");

        if (background != ('rgb(255, 255, 255)')) {
            $(".buttons-csv").css("color", "white");
        }
    </script>
<?php } ?>

<?php if ($this->uri->segment(2) == "was") { ?>
    <script>
        $('.tablewas').DataTable({
            "pageLength": 25,
            responsive: false,
            ordering: false,
            "scrollY":        "400px",
            "scrollCollapse": true,
            "paging":         false,
            "scrollX": true,
            dom: 'Bfrtip',
            buttons: [
                'csv'
            ]
        });

        $('.tablesummary').DataTable({
            info: false,
            searching: false,
            ordering: false,
            "paging":         false,
            dom: 'Bfrtip',
            buttons: [
                'csv'
            ]
        });

        // using this to change color of csv-button if dark mode is chosen
        var background = $('body').css( "background-color");

        if (background != ('rgb(255, 255, 255)')) {
            $(".buttons-csv").css("color", "white");
        }
    </script>
<?php } ?>

<script>
        function qsl_rcvd(id, method) {
            var baseURL= "<?php echo base_url();?>";
            $.ajax({
                url: baseURL + 'index.php/qso/qsl_rcvd_ajax',
                type: 'post',
                data: {'id': id,
                    'method': method
                },
                success: function(data) {
                    if (data.message == 'OK') {
                        $("#qso_" + id).find("td:eq(8)").find("span:eq(1)").attr('class', 'qsl-green'); // Paints arrow green
                        $(".qsl_" + id).remove(); // removes choice from menu
                    }
                    else {
                        $(".bootstrap-dialog-message").append('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>You are not allowed to update QSL status!</div>');
                    }
                }
            });
        }

        // Function: qsl_requested
        // Marks QSL card requested against the QSO.
        function qsl_requested(id, method) {
            var baseURL= "<?php echo base_url();?>";
            $.ajax({
                url: baseURL + 'index.php/qso/qsl_requested_ajax',
                type: 'post',
                data: {'id': id,
                    'method': method
                },
                success: function(data) {
                    if (data.message == 'OK') {
                        $("#qso_" + id).find("td:eq(8)").find("span:eq(0)").attr('class', 'qsl-yellow'); // Paints arrow green
                    }
                    else {
                        $(".bootstrap-dialog-message").append('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>You are not allowed to update QSL status!</div>');
                    }
                }
            });
        }

        // Function: qsl_ignore
        // Marks QSL card ignore against the QSO.
        function qsl_ignore(id, method) {
            var baseURL= "<?php echo base_url();?>";
            $.ajax({
                url: baseURL + 'index.php/qso/qsl_ignore_ajax',
                type: 'post',
                data: {'id': id,
                    'method': method
                },
                success: function(data) {
                    if (data.message == 'OK') {
                        $("#qso_" + id).find("td:eq(8)").find("span:eq(0)").attr('class', 'qsl-red'); // Paints arrow green
                    }
                    else {
                        $(".bootstrap-dialog-message").append('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>You are not allowed to update QSL status!</div>');
                    }
                }
            });
        }

        function qso_delete(id, call) {
            BootstrapDialog.confirm({
                title: 'DANGER',
                message: 'Warning! Are you sure you want delete QSO with ' + call + '?' ,
                type: BootstrapDialog.TYPE_DANGER,
                closable: true,
                draggable: true,
                btnOKClass: 'btn-danger',
                callback: function(result) {
                    if(result) {
                        $(".edit-dialog").modal('hide');
                        $(".qso-dialog").modal('hide');
                        var baseURL= "<?php echo base_url();?>";
                        $.ajax({
                            url: baseURL + 'index.php/qso/delete_ajax',
                            type: 'post',
                            data: {'id': id
                            },
                            success: function(data) {
                                $(".alert").remove();
                                $(".bootstrap-dialog-message").prepend('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>The contact with ' + call + ' has been deleted!</div>');
                                $("#qso_" + id).remove(); // removes qso from table in dialog
                            }
                        });
                    }
                }
            });
        }

        function qso_edit(id) {
            var baseURL= "<?php echo base_url();?>";
            $.ajax({
                url: baseURL + 'index.php/qso/edit_ajax',
                type: 'post',
                data: {'id': id
                },
                success: function(html) {
                    BootstrapDialog.show({
                        title: 'QSO Data',
                        cssClass: 'edit-dialog',
                        size: BootstrapDialog.SIZE_WIDE,
                        nl2br: false,
                        message: html,
                        onshown: function(dialog) {
                            var state = $("#input_usa_state option:selected").text();
                            if (state != "") {
                                $("#stationCntyInput").prop('disabled', false);
                                selectize_usa_county();
                            }

                            $('#input_usa_state').change(function(){
                                var state = $("#input_usa_state option:selected").text();
                                if (state != "") {
                                    $("#stationCntyInput").prop('disabled', false);

                                    selectize_usa_county();

                                } else {
                                    $("#stationCntyInput").prop('disabled', true);
                                    //$('#stationCntyInput')[0].selectize.destroy();
                                    $("#stationCntyInput").val("");
                                }
                            });

                            $('#sota_ref').selectize({
                                maxItems: 1,
                                closeAfterSelect: true,
                                loadThrottle: 250,
                                valueField: 'name',
                                labelField: 'name',
                                searchField: 'name',
                                options: [],
                                create: false,
                                load: function(query, callback) {
                                    if (!query || query.length < 3) return callback();  // Only trigger if 3 or more characters are entered
                                    $.ajax({
                                        url: baseURL+'index.php/qso/get_sota',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            query: query,
                                        },
                                        error: function() {
                                            callback();
                                        },
                                        success: function(res) {
                                            callback(res);
                                        }
                                    });
                                }
                            });

                            $('#darc_dok').selectize({
                                maxItems: 1,
                                closeAfterSelect: true,
                                loadThrottle: 250,
                                valueField: 'name',
                                labelField: 'name',
                                searchField: 'name',
                                options: [],
                                create: false,
                                load: function(query, callback) {
                                    if (!query) return callback();  // Only trigger if 3 or more characters are entered
                                    $.ajax({
                                        url: baseURL+'index.php/qso/get_dok',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            query: query,
                                        },
                                        error: function() {
                                            callback();
                                        },
                                        success: function(res) {
                                            callback(res);
                                        }
                                    });
                                }
                            });
                        },
                    });
                }
            });
        }

        function selectize_usa_county() {
            var baseURL= "<?php echo base_url();?>";
            $('#stationCntyInput').selectize({
				delimiter: ';',
                maxItems: 1,
                closeAfterSelect: true,
                loadThrottle: 250,
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                options: [],
                create: false,
                load: function(query, callback) {
                    var state = $("#input_usa_state option:selected").text();

                    if (!query || state == "") return callback();
                    $.ajax({
                        url: baseURL+'index.php/qso/get_county',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            query: query,
                            state: state,
                        },
                        error: function() {
                            callback();
                        },
                        success: function(res) {
                            callback(res);
                        }
                    });
                }
            });
        }

        function qso_save() {
            var baseURL= "<?php echo base_url();?>";
            var myform = document.getElementById("qsoform");
            var fd = new FormData(myform);
            $.ajax({
                url: baseURL + 'index.php/qso/qso_save_ajax',
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (dataofconfirm) {
                    $(".edit-dialog").modal('hide');
                    $(".qso-dialog").modal('hide');
                    <?php if ($this->uri->segment(1) != "search" && $this->uri->segment(2) != "filter" && $this->uri->segment(1) != "qso") { ?>location.reload();<?php } ?>
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
        </script>
    <?php if ($this->uri->segment(1) == "timeline") { ?>
        <script>
            $('.timelinetable').DataTable({
                "pageLength": 25,
                responsive: false,
                ordering: false,
                "scrollY":        "500px",
                "scrollCollapse": true,
                "paging":         false,
                "scrollX": true,
                dom: 'Bfrtip',
                buttons: [
                    'csv'
                ]
            });

            // using this to change color of csv-button if dark mode is chosen
            var background = $('body').css( "background-color");

            if (background != ('rgb(255, 255, 255)')) {
                $(".buttons-csv").css("color", "white");
            }

            function displayTimelineContacts(querystring, band, mode, type) {
                var baseURL= "<?php echo base_url();?>";
                $.ajax({
                    url: baseURL + 'index.php/timeline/details',
                    type: 'post',
                    data: {'Querystring': querystring,
                        'Band': band,
                        'Mode': mode,
                        'Type': type
                    },
                    success: function(html) {
                        BootstrapDialog.show({
                            title: 'QSO Data',
                            size: BootstrapDialog.SIZE_WIDE,
                            cssClass: 'qso-was-dialog',
                            nl2br: false,
                            message: html,
                            buttons: [{
                                label: 'Close',
                                action: function (dialogItself) {
                                    dialogItself.close();
                                }
                            }]
                        });
                    }
                });
            }
        </script>
        <?php } ?>

    <?php if ($this->uri->segment(1) == "mode") { ?>
		<script src="<?php echo base_url(); ?>assets/js/sections/mode.js"></script>
    <?php } ?>

<?php if ($this->uri->segment(1) == "accumulated") { ?>
    <script src="<?php echo base_url(); ?>assets/js/chart.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/sections/accumulatedstatistics.js"></script>
<?php } ?>

<?php if ($this->uri->segment(1) == "timeplotter") { ?>
    <script src="<?php echo base_url(); ?>assets/js/highstock.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/highstock/exporting.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/highstock/offline-exporting.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/highstock/export-data.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/sections/timeplot.js"></script>
<?php } ?>

<?php if ($this->uri->segment(1) == "qsl") { ?>
    <script>
        $('.qsltable').DataTable({
            "pageLength": 25,
            responsive: false,
            ordering: false,
            "scrollY":        "500px",
            "scrollCollapse": true,
            "paging":         false,
            "scrollX": true
        });
    </script>
<?php } ?>

<?php if ($this->uri->segment(1) == "kml") { ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker({
                format: 'DD/MM/YYYY',
            });
        });
        $(function () {
            $('#datetimepicker2').datetimepicker({
                format: 'DD/MM/YYYY',
            });
        });
    </script>
<?php } ?>

<script>
function viewQsl(picture, callsign) {
            var baseURL= "<?php echo base_url();?>";
            var $textAndPic = $('<div></div>');
                $textAndPic.append('<img class="img-fluid" style="height:auto;width:auto;"src="'+baseURL+'/assets/qslcard/'+picture+'" />');
            var title = '';
            if (callsign == null) {
                title = 'QSL Card';
            } else {
                title = 'QSL Card for ' + callsign;
            }

            BootstrapDialog.show({
                title: title,
                size: BootstrapDialog.SIZE_WIDE,
                message: $textAndPic,
                buttons: [{
                    label: 'Close',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                }]
            });
        }
</script>
<script>
function deleteQsl(id) {
            BootstrapDialog.confirm({
                title: 'DANGER',
                message: 'Warning! Are you sure you want to delete this QSL card?'  ,
                type: BootstrapDialog.TYPE_DANGER,
                closable: true,
                draggable: true,
                btnOKClass: 'btn-danger',
                callback: function(result) {
                    if(result) {
                        var baseURL= "<?php echo base_url();?>";
                        $.ajax({
                            url: baseURL + 'index.php/qsl/delete',
                            type: 'post',
                            data: {'id': id
                            },
                            success: function(data) {
                                $("#" + id).parent("tr:first").remove(); // removes qsl from table

                                // remove qsl from carousel
                                $(".carousel-indicators li:last-child").remove();
                                $(".carouselimageid_"+id).remove();
                                $('#carouselExampleIndicators').find('.carousel-item').first().addClass('active');

                                // remove table and hide tab if all qsls are deleted
                                if ($('.qsltable tr').length == 1) {
                                    $('.qsltable').remove();
                                    $('.qslcardtab').attr('hidden','');
                                }
                            }
                        });
                    }
                }
            });
        }
</script>

<script>
  /*
   * Used to fetch QSOs from the logbook in the awards
   */
    function displayContacts(searchphrase, band, mode, type) {
        var baseURL = "<?php echo base_url();?>";
        $.ajax({
            url: baseURL + 'index.php/awards/qso_details_ajax',
            type: 'post',
            data: {
                'Searchphrase': searchphrase,
                'Band': band,
                'Mode': mode,
                'Type': type
            },
            success: function (html) {
                BootstrapDialog.show({
                    title: 'QSO Data',
                    size: BootstrapDialog.SIZE_WIDE,
                    cssClass: 'qso-dialog',
                    nl2br: false,
                    message: html,
                    buttons: [{
                        label: 'Close',
                        action: function (dialogItself) {
                            dialogItself.close();
                        }
                    }]
                });
            }
        });
    }

    function uploadQsl() {
        var baseURL= "<?php echo base_url();?>";
        var formdata = new FormData(document.getElementById("fileinfo"));

        $.ajax({
            url: baseURL + 'index.php/qsl/uploadqsl',
            type: 'post',
            data: formdata,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.status.front.status == 'Success') {
                    if ($('.qsltable').length > 0) {
                        $('.qsltable tr:last').after('<tr><td style="text-align: center">'+data.status.front.filename+'</td>' +
                            '<td id="'+data.status.front.insertid+'"style="text-align: center"><button onclick="deleteQsl('+data.status.front.insertid+');" class="btn btn-sm btn-danger">Delete</button></td>' +
                            '<td style="text-align: center"><button onclick="viewQsl(\'' + data.status.front.filename + '\')" class="btn btn-sm btn-success">View</button></td>'+
                            '</tr>');
                        var quantity = $(".carousel-indicators li").length;
                        $(".carousel-indicators").append('<li data-target="#carouselExampleIndicators" data-slide-to="'+quantity+'"></li>');
                        $(".carousel-inner").append('<div class="carousel-item carouselimageid_'+data.status.front.insertid+'"><img class="d-block w-100" src="'+baseURL+'/assets/qslcard/'+data.status.front.filename+'" alt="QSL picture #'+(quantity+1)+'"></div>');
                        $("#qslcardfront").val(null);
                    }
                    else {
                        $("#qslupload").prepend('<table style="width:100%" class="qsltable table table-sm table-bordered table-hover table-striped table-condensed">'+
                            '<thead>'+
                               '<tr>'+
                            '<th style="text-align: center">QSL image file</th>'+
                            '<th style="text-align: center"></th>'+
                            '<th style="text-align: center"></th>'+
                            '</tr>'+
                            '</thead><tbody>'+
                                '<tr><td style="text-align: center">'+data.status.front.filename+'</td>' +
                            '<td id="'+data.status.front.insertid+'"style="text-align: center"><button onclick="deleteQsl('+data.status.front.insertid+');" class="btn btn-sm btn-danger">Delete</button></td>' +
                            '<td style="text-align: center"><button onclick="viewQsl(\'' + data.status.front.filename + '\')" class="btn btn-sm btn-success">View</button></td>'+
                            '</tr>'+
                        '</tbody></table>');
                        $('.qslcardtab').removeAttr('hidden');
                        var quantity = $(".carousel-indicators li").length;
                        $(".carousel-indicators").append('<li class="active" data-target="#carouselExampleIndicators" data-slide-to="'+quantity+'"></li>');
                        $(".carousel-inner").append('<div class="active carousel-item carouselimageid_'+data.status.front.insertid+'"><img class="d-block w-100" src="'+baseURL+'/assets/qslcard/'+data.status.front.filename+'" alt="QSL picture #'+(quantity+1)+'"></div>');
                        $(".carouselExampleIndicators").carousel();
                        $("#qslcardfront").val(null);
                    }

                } else {
                    $("#qslupload").append('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        data.status.front +
                        '</div>');
                }
                if (data.status.back.status == 'Success') {
                    var qsoid = $("#qsoid").text();
                    if ($('.qsltable').length > 0) {
                        $('.qsltable tr:last').after('<tr><td style="text-align: center">'+data.status.back.filename+'</td>' +
                            '<td id="'+data.status.back.insertid+'"style="text-align: center"><button onclick="deleteQsl('+data.status.back.insertid+');" class="btn btn-sm btn-danger">Delete</button></td>' +
                            '<td style="text-align: center"><button onclick="viewQsl(\'' + data.status.back.filename + '\')" class="btn btn-sm btn-success">View</button></td>'+
                            '</tr>');
                        var quantity = $(".carousel-indicators li").length;
                        $(".carousel-indicators").append('<li data-target="#carouselExampleIndicators" data-slide-to="'+quantity+'"></li>');
                        $(".carousel-inner").append('<div class="carousel-item carouselimageid_'+data.status.back.insertid+'"><img class="d-block w-100" src="'+baseURL+'/assets/qslcard/'+data.status.back.filename+'" alt="QSL picture #'+(quantity+1)+'"></div>');
                        $("#qslcardback").val(null);
                    }
                    else {
                        $("#qslupload").prepend('<table style="width:100%" class="qsltable table table-sm table-bordered table-hover table-striped table-condensed">'+
                            '<thead>'+
                            '<tr>'+
                            '<th style="text-align: center">QSL image file</th>'+
                            '<th style="text-align: center"></th>'+
                            '<th style="text-align: center"></th>'+
                            '</tr>'+
                            '</thead><tbody>'+
                            '<tr><td style="text-align: center">'+data.status.back.filename+'</td>' +
                            '<td id="'+data.status.back.insertid+'"style="text-align: center"><button onclick="deleteQsl('+data.status.back.insertid+');" class="btn btn-sm btn-danger">Delete</button></td>' +
                            '<td><button onclick="viewQsl(\'' + data.status.back.filename + '\')" class="btn btn-sm btn-success">View</button></td>'+
                            '</tr>'+
                            '</tbody></table>');
                        $('.qslcardtab').removeAttr('hidden');
                        var quantity = $(".carousel-indicators li").length;
                        $(".carousel-indicators").append('<li class="active" data-target="#carouselExampleIndicators" data-slide-to="'+quantity+'"></li>');
                        $(".carousel-inner").append('<div class="active carousel-item carouselimageid_'+data.status.back.insertid+'"><img class="d-block w-100" src="'+baseURL+'/assets/qslcard/'+data.status.back.filename+'" alt="QSL picture #'+(quantity+1)+'"></div>');
                        $(".carouselExampleIndicators").carousel();
                        $("#qslcardback").val(null);
                    }
                } else {
                    $("#qslupload").append('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        data.status.back +
                        '</div>');
                }
            }
        });
    }
</script>
<script>

	function addQsosToQsl(filename) {
		var title = 'Add additional QSOs to a QSL Card';

		var baseURL= "<?php echo base_url();?>";
		$.ajax({
			url: baseURL + 'index.php/qsl/loadSearchForm',
			type: 'post',
			data: {'filename': filename},
			success: function(html) {
				BootstrapDialog.show({
					title: title,
					size: BootstrapDialog.SIZE_WIDE,
					cssClass: 'qso-search_results',
					nl2br: false,
					message: html,
					buttons: [{
						label: 'Close',
						action: function (dialogItself) {
							dialogItself.close();
						}
					}]
				});
			}
		});
	}

	function addQsoToQsl(qsoid, filename, id) {
		var title = 'Add additional QSOs to a QSL Card';

		var baseURL= "<?php echo base_url();?>";
		$.ajax({
			url: baseURL + 'index.php/qsl/addQsoToQsl',
			type: 'post',
			data: {'filename': filename, 'qsoid': qsoid},
			success: function(html) {
				if (html.status == 'Success') {
					location.reload();
				} else {
					$(".alert").remove();
					$('#searchresult').prepend('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Something went wrong. Please try again!</div>');
				}
			}
		});
	}

	function searchAdditionalQsos(filename) {
		var baseURL= "<?php echo base_url();?>";
		$.ajax({
			url: baseURL + 'index.php/qsl/searchQsos',
			type: 'post',
			data: {'callsign': $('#callsign').val(), 'filename': filename},
			success: function(html) {
				$('#searchresult').empty();
				$('#searchresult').append(html);
			}
		});
	}
</script>
<?php if ($this->uri->segment(1) == "contesting") { ?>
    <script src="<?php echo base_url() ;?>assets/js/sections/contesting.js"></script>
    <script>
        function logQso() {
            if ($("#callsign").val().length > 0) {

                $('.callsign-suggestions').text("");

                var table = $('.qsotable').DataTable();

                var data = [[$("#start_date").val()+ ' ' + $("#start_time").val(),
                    $("#callsign").val().toUpperCase(),
                    $("#band").val(),
                    $("#mode").val(),
                    $("#rst_sent").val(),
                    $("#rst_recv").val(),
                    $("#exch_sent").val(),
                    $("#exch_recv").val()]];

                table.rows.add(data).draw();

                var baseURL= "<?php echo base_url();?>";
                var formdata = new FormData(document.getElementById("qso_input"));
                $.ajax({
                    url: baseURL + 'index.php/qso/saveqso',
                    type: 'post',
                    data: formdata,
                    processData: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    success: function (html) {
                        if (localStorage.getItem("qso") == null) {
                            localStorage.setItem("qso", $("#start_date").val()+ ' ' + $("#start_time").val() + ',' + $("#callsign").val().toUpperCase() + ',' + $("#contestname").val());
                        }

                        $('#name').val("");

                        $('#callsign').val("");
                        $('#comment').val("");
                        $('#exch_recv').val("");
                        if ($('input[name=exchangeradio]:checked', '#qso_input').val() == "serial") {
                            $("#exch_sent").val(+$("#exch_sent").val() + 1);
                        }
                        $("#callsign").focus();

                        // Store contest session
                        localStorage.setItem("contestid", $("#contestname").val());
                        localStorage.setItem("exchangetype", $('input[name=exchangeradio]:checked', '#qso_input').val());
                        localStorage.setItem("exchangesent", $("#exch_sent").val());
                    }
                });
            }
        }

        // We are restoring the settings in the contest logging form here
        function restoreContestSession() {
            var contestname = localStorage.getItem("contestid");

            if (contestname != null) {
                $("#contestname").val(contestname);
            }

            var exchangetype = localStorage.getItem("exchangetype");

            if (exchangetype == "other") {
                $("[name=exchangeradio]").val(["other"]);
            }

            var exchangesent = localStorage.getItem("exchangesent");

            if (exchangesent != null) {
                $("#exch_sent").val(exchangesent);
            }

            if (localStorage.getItem("qso") != null) {
                var baseURL= "<?php echo base_url();?>";
                //alert(localStorage.getItem("qso"));
                var qsodata = localStorage.getItem("qso");
                $.ajax({
                    url: baseURL + 'index.php/contesting/getSessionQsos',
                    type: 'post',
                    data: {'qso': qsodata,},
                    success: function (html) {
                        var mode = '';
                        var sentexchange = '';
                        var receivedexchange = '';
                        $.each(html, function(){
                            if (this.col_submode == null || this.col_submode == '') {
                                mode = this.col_mode;
                            } else {
                                mode = this.col_submode;
                            }

                            if (this.col_srx == null || this.col_srx == '') {
                                receivedexchange = this.col_srx_string;
                            } else {
                                receivedexchange = this.col_srx;
                            }

                            if (this.col_stx == null || this.col_stx == '') {
                                sentexchange = this.col_stx_string;
                            } else {
                                sentexchange = this.col_stx;
                            }

                            $(".qsotable tbody").prepend('<tr>' +
                                '<td>'+ this.col_time_on + '</td>' +
                                '<td>'+ this.col_call + '</td>' +
                                '<td>'+ this.col_band + '</td>' +
                                '<td>'+ mode + '</td>' +
                                '<td>'+ this.col_rst_sent + '</td>' +
                                '<td>'+ this.col_rst_rcvd + '</td>' +
                                '<td>'+ sentexchange + '</td>' +
                                '<td>'+ receivedexchange + '</td>' +
                                '</tr>');
                        });
                        if (!$.fn.DataTable.isDataTable('.qsotable')) {
                            $('.qsotable').DataTable({
                                "pageLength": 25,
                                responsive: false,
                                "scrollY":        "400px",
                                "scrollCollapse": true,
                                "paging":         false,
                                "scrollX": true,
                                "order": [[ 0, "desc" ]]
                            });
                        }
                    }
                });
            }
        }
    </script>

<?php } ?>

<?php if ($this->uri->segment(1) == "station") { ?>
<script>
    var baseURL= "<?php echo base_url();?>";

	var state = $("#StateHelp option:selected").text();
	if (state != "") {
		$("#stationCntyInput").prop('disabled', false);
		station_profile_selectize_usa_county();
	}

    $('#StateHelp').change(function(){
        var state = $("#StateHelp option:selected").text();
        if (state != "") {
            $("#stationCntyInput").prop('disabled', false);
			station_profile_selectize_usa_county();
        } else {
            $("#stationCntyInput").prop('disabled', true);
            //$('#stationCntyInput')[0].selectize.destroy();
            $("#stationCntyInput").val("");
        }
    });

    function station_profile_selectize_usa_county() {
		$('#stationCntyInput').selectize({
			maxItems: 1,
			closeAfterSelect: true,
			loadThrottle: 250,
			valueField: 'name',
			labelField: 'name',
			searchField: 'name',
			options: [],
			create: false,
			load: function(query, callback) {
				var state = $("#StateHelp option:selected").text();

				if (!query || state == "") return callback();
				$.ajax({
					url: baseURL+'index.php/station/get_county',
					type: 'GET',
					dataType: 'json',
					data: {
						query: query,
						state: state,
					},
					error: function() {
						callback();
					},
					success: function(res) {
						callback(res);
					}
				});
			}
		});
	}
</script>

<?php } ?>

<?php if ($this->uri->segment(2) == "counties" || $this->uri->segment(2) == "counties_details") { ?>
<script>
    $('.countiestable').DataTable({
        "pageLength": 25,
        responsive: false,
        ordering: false,
        "scrollY":        "390px",
        "scrollCollapse": true,
        "paging":         false,
        "scrollX": true,
        dom: 'Bfrtip',
        buttons: [
            'csv'
        ]
    });
    // using this to change color of csv-button if dark mode is chosen
    var background = $('body').css( "background-color");

    if (background != ('rgb(255, 255, 255)')) {
        $(".buttons-csv").css("color", "white");
    }

    function displayCountyContacts(state, county) {
        var baseURL= "<?php echo base_url();?>";
        $.ajax({
            url: baseURL + 'index.php/awards/counties_details_ajax',
            type: 'post',
            data: {'State': state, 'County': county },
            success: function(html) {
                BootstrapDialog.show({
                    title: 'QSO Data',
                    size: BootstrapDialog.SIZE_WIDE,
                    cssClass: 'qso-counties-dialog',
                    nl2br: false,
                    message: html,
                    buttons: [{
                        label: 'Close',
                        action: function (dialogItself) {
                            dialogItself.close();
                        }
                    }]
                });
            }
        });
    }
</script>
<?php } ?>

<?php if ($this->uri->segment(2) == "sig_details") { ?>
	<script>
		$('.tablesig').DataTable({
			"pageLength": 25,
			responsive: false,
			ordering: false,
			"scrollY":        "400px",
			"scrollCollapse": true,
			"paging":         false,
			"scrollX": true,
			dom: 'Bfrtip',
			buttons: [
				'csv'
			]
		});

		// using this to change color of csv-button if dark mode is chosen
		var background = $('body').css( "background-color");

		if (background != ('rgb(255, 255, 255)')) {
			$(".buttons-csv").css("color", "white");
		}

	</script>
<?php } ?>

<?php if ($this->uri->segment(1) == "contesting" && $this->uri->segment(2) == "add") { ?>
	<script src="<?php echo base_url() ;?>assets/js/sections/contestingnames.js"></script>
<?php } ?>

<?php if ($this->uri->segment(1) == "qslprint") { ?>
	<script>
		function deleteFromQslQueue(id) {
			BootstrapDialog.confirm({
				title: 'DANGER',
				message: 'Warning! Are you sure you want to removes this QSL from the queue?',
				type: BootstrapDialog.TYPE_DANGER,
				closable: true,
				draggable: true,
				btnOKClass: 'btn-danger',
				callback: function(result) {
					$.ajax({
						url: base_url + 'index.php/qslprint/delete_from_qsl_queue',
						type: 'post',
						data: {'id': id	},
						success: function(html) {
							location.reload();
						}
					});
				}
			});
		}

		$(".station_id").change(function(){
			var station_id = $(".station_id").val();
			$.ajax({
				url: base_url + 'index.php/qslprint/get_qsos_for_print_ajax',
				type: 'post',
				data: {'station_id': station_id},
				success: function(html) {
					$('.resulttable').empty();
					$('.resulttable').append(html);
				}
			});
		});
	</script>
<?php } ?>
  </body>
</html>
