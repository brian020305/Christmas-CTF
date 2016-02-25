<?php
$db_rank_sql = "SELECT *, FIND_IN_SET( score, (
SELECT GROUP_CONCAT( score
ORDER BY score DESC )
FROM ctf_team )
) AS rank
FROM ctf_team ORDER BY rank,last_auth,reg_timestamp LIMIT 10";
$db_rank_result = $this->db->query($db_rank_sql);


?>
<!-- RANK 시작 -->
          <div class="rank">
            <div class="box_div">
              <div class="box_title">
                <span>RANKING</span>
              </div>
              <span class="box_date"><?=date('Y년 m월 d일 H시 i분 s초')?></span>
              <div class="box_content">
                <table class="rank_tb" cellspacing=0 cellpadding=0>
                  <thead>
                    <tr>
                      <td class="rank_tb_rank">RANK</td>
                      <td class="rank_tb_name">NAME</td>
                      <td class="rank_tb_point">POINT</td>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- 등수 출력 여기부터 반복 -->
		    <?php
		    foreach($db_rank_result->result() as $row) {
		    ?>
                    <tr>
                      <td class="rank_tb_body_rank"><?=$row->rank?>등</td>
                      <td><?=$row->team_name?></td>
                      <td><?=$row->score?></td>
                    </tr>
                    <!-- 등수출력 반복 끝 -->
		    <?php
		    }
		    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- RANK 끝 -->
