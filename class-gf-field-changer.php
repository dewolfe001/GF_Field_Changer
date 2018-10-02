<?php
 
class GF_Field_Changer extends GFAddOn {
 
    protected $_version = GF_IMAGE_FIELDS_VERSION;
    protected $_min_gravityforms_version = '1.9';
 
    private static $_instance = null;
 
    /**
     * Get an instance of this class.
     *
     * @return GFCustomEntryLimit
     */
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new GF_Field_Changer();
        }       
        return self::$_instance;
    }
	// put in field filters to affect the form component elements and add a new dropdown

	// variables and constants

	public function init() {
		// Bail on non admin.
		if ( ! is_admin() ) {
			return;
		}

		// variables

		// actions
		// add_action( 'gform_editor_js', array($this, 'editor_script' ), 9, 2 );
		add_action( 'gform_field_standard_settings', array($this, 'changer_dropdown'), 11, 2 );
        add_action( 'gform_editor_js', array( $this, 'editor_script' ) );



		// filters
		// add_filter( 'gform_field_container', array($this, 'changer_filter'), 10, 6 );
		add_filter( 'gform_tooltips', array($this, 'add_encryption_tooltips'));

		// workers
	}
	
	// actions
	//Ajax calls
	public static function change_field() {
		check_ajax_referer( 'rg_change_field', 'rg_change_field' );
		$field_json = stripslashes_deep( $_POST['field'] );

		$field_properties = GFCommon::json_decode( $field_json, true );

		$field = GF_Fields::create( $field_properties );
		$field->sanitize_settings();

		$index = rgpost( 'index' );

		if ( $index != 'undefined' ) {
			$index = absint( $index );
		}

		require_once( GFCommon::get_base_path() . '/form_display.php' );

		$form_id = absint( rgpost( 'form_id' ) );
		$form    = GFFormsModel::get_form_meta( $form_id );

		$field_html      = GFFormDisplay::get_field( $field, '', true, $form );
		$field_html_json = json_encode( $field_html );

		$field_json = json_encode( $field );

		die( "EndAddField($field_json, " . $field_html_json . ", $index);" );
	}


	public function changer_dropdown( $position, $form_id ) {
		//create settings on position 25 (right after Field Label)
		if ( $position == 10 ) {
			?>
			<li class="label_setting field_setting">
				<label for="field_admin_label" class="section_label">
					<?php esc_html_e( 'Change Field', 'gravityforms' ); ?>
					<?php gform_tooltip( 'form_field_changer_value' ); ?>
				</label>
				<select class="field_changer_value">
				<?php
				$field_groups = GFFormDetail::get_field_groups();
				foreach ($field_groups as $group) {
					print '<optgroup label="'.$group['label'].'"></optgroup>';
					foreach ($group['fields'] as $one_field) {
						print '<option value="'.$one_field['data-type'].'">'.$one_field['value'].'</option>';
					}
				}
				?>
				</select>
			</li>
			<?php
		}
	}

	public function editor_script(){
		?>
		<script type='text/javascript'>	 
			jQuery(document).ready(function($) {
				$('select.field_changer_value').on('change', function() {
				  var gfield = $(this).closest(".gfield");				  
				  var type = $(this).find('option:selected').val();
				  SetFieldChanger(gfield, type);
				});
			});


		function SetFieldChanger(fieldChanger, type){
			var indextext = fieldChanger.attr("id");
			var index = indextext.split("_")[1];
			var findelement = "#" + fieldChanger.attr("id") + " :input";

			var cssindex = $( "#" + indextext ).index(); 
			
			var olditems = $(findelement).map(function(index, elm) {
				return {name: elm.name, type:elm.type, value: $(elm).val()};
			});

			console.log("line 94 - " +  fieldChanger.attr("id"));
			console.log("line 95 - " +  JSON.stringify(olditems));
			console.log("line 96 - " +  JSON.stringify(form));
			console.log("line 97 - " +  indextext);

			/* TODO - real work */

			// get the new form element

			// CRIB : START

			if (!CanFieldBeAdded(type)) {
				jQuery('#gform_adding_field_spinner').remove();
				return;
			}

			if (gf_vars["currentlyAddingField"] == true)
				return;

			gf_vars["currentlyAddingField"] = true;

			// delete the existing one			
			$("#" + indextext + " *").remove();
			alert(indextext + " for CreateField( " + index + ", " + type + ", " + cssindex + ")");
			var field = CreateField( index, type, cssindex );

			var mysack = new sack("<?php echo admin_url( 'admin-ajax.php' )?>");
			mysack.execute = 1;
			mysack.method = 'POST';
			mysack.setVar("action", "rg_change_input_type");
			mysack.setVar("rg_change_input_type", "<?php echo wp_create_nonce( 'rg_change_input_type' ) ?>");
			mysack.setVar("index", index);
			mysack.setVar("field", jQuery.toJSON(field));
			mysack.setVar('form_id', <?php echo $_GET['id']; ?>);  
			mysack.onError = function () {
				alert(<?php
					echo json_encode( esc_html__( 'Ajax error while adding field', 'gravityforms' ) ); ?>)
			};
			mysack.runAJAX();

			// start populating
			CreateDefaultValuesUI(fieldChanger);
			CreatePlaceholdersUI(fieldChanger);
			CreateInputNames(fieldChanger);		
			// do the re-population

			for (item in olditems) { 
				console.log( "#" + fieldChanger.attr("id") + " of " + item.type + ' [name="' + item.name + "'] gets " + item.value);
				$("#" + fieldChanger.attr("id") + ' [name="' + item.name + "']").val(item.value);
			}

			return true;
			
			// CRIB : END 
		}

		function dump(obj) {
			var out = '';
			for (var i in obj) {
				out += i + ": " + obj[i] + "\n";
			}
			return out;
		}
		</script>
		<?php
	}

	// filters
	public function changer_filter( $field_container, $field, $form, $css_class, $style, $field_content ) {
		return $field_container;
	}

	public function add_encryption_tooltips( $tooltips ) {
	   $tooltips['form_field_changer_value'] = "<h6>Changer</h6>Change this field type to a different field type";
	   return $tooltips;
	}
 
	// workers

}
