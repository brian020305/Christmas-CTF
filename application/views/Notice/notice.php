<?php
$db_notice_sql = "SELECT * FROM ctf_notice ORDER BY no DESC";
$db_notice_result = $this->db->query($db_notice_sql);

?>


<div class="notice">
<?php
foreach($db_notice_result->result() as $row) {
?>
  <div class="box_div">
    <div class="box_title">
      <span><?=$row->title?></span>
    </div>
    <span class="box_date"><?=$row->reg_timestamp?></span>
    <div class="box_content">
      <?=$row->content?>
    </div>
  </div>
<?php
}
?>
</div>
