<?php

require_once('db_functions.php');

/***
 * Support user updates on ConReg conreg_members table.
 */
 
class ConRegMember
{
  protected $connection;
  
  /**
   * Constructs a new Member object.
   */
  public function __construct()
  {
    $this->connection = new mysqli(CONREG_DBHOSTNAME, CONREG_DBUSERID, CONREG_DBPASSWORD, CONREG_DBDB);
  }

  /**
   * Get the ConReg member ID for badge ID.
   */
  public function getMid($badgeId)
  {
    $sql = "SELECT mid FROM conreg_zambia WHERE badgeid=?";
    $stmt = $this->connection->prepare($sql);
    $stmt->bind_param('s', $badgeId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
      return ($result->fetch_object()->mid);
    }
    return NULL;
  }
  
  public function updateMember($badgeId, $fields)
  {
    $mid = $this->getMid($badgeId);
    if (!empty($mid)) {

      $query_preable = "UPDATE conreg_members SET ";
      $query_portion_arr = [];
      $query_param_arr = [];
      $query_param_type_str = "";
      foreach ($fields as $key=>$val) {
        push_query_arrays($val, $key, 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
      }
      $query_param_arr[] = $mid;
      $query_param_type_str .= 'i';
      $query = $query_preable . implode(', ', $query_portion_arr) . " WHERE mid = ?";
      $stmt = $this->connection->prepare($query);
      $stmt->bind_param($query_param_type_str, ...$query_param_arr);
      $result = $stmt->execute();
      $stmt->close();
      return $result;
    }
  }
}
