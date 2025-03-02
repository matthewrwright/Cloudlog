<?php

class Qslprint_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	function mark_qsos_printed($station_id2 = NULL) {
		$CI =& get_instance();
		$CI->load->model('Stations');
		$station_id = $CI->Stations->find_active();

		$data = array(
	        'COL_QSLSDATE' => date('Y-m-d'),
	        'COL_QSL_SENT' => "Y",
	        'COL_QSL_SENT_VIA' => "B",
		);

		$this->db->where_in("COL_QSL_SENT", array("R","Q"));

		if ($station_id2 == NULL) {
			$this->db->where("station_id", $station_id);
		} else {
			$this->db->where("station_id", $station_id2);
		}

		$this->db->update($this->config->item('table_name'), $data);
	}

	/*
	 * We list out the QSL's ready for print.
	 * station_id is not provided when loading page.
	 * It will be provided when calling the function when the dropdown is changed and the javascript fires
	 */
	function get_qsos_for_print($station_id = 'All') {
		if ($station_id != 'All') {
			$this->db->where($this->config->item('table_name').'.station_id', $station_id);
		}

		$this->db->join('station_profile', 'station_profile.station_id = '.$this->config->item('table_name').'.station_id');
		$this->db->where_in('COL_QSL_SENT', array('R', 'Q'));
		$this->db->order_by("COL_TIME_ON", "ASC");
		$query = $this->db->get($this->config->item('table_name'));

		return $query;
	}

	function get_qsos_for_print_ajax($station_id) {
		$query = $this->get_qsos_for_print($station_id);

		return $query;
	}

	function delete_from_qsl_queue($id) {
		$data = array(
			'COL_QSL_SENT' => "N",
		);

		$this->db->where("COL_PRIMARY_KEY", $id);
		$this->db->update($this->config->item('table_name'), $data);

		return true;
	}
}

?>
