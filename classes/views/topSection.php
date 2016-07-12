<div id="main_page_header">
    <ul>
        <?php for($i=1; $i<=$number_of_bookers;$i++): ?>
            <li><a href="index.php?action=main&booker=<?php echo $i; ?>">Boardroom <?php echo $i; ?></a></li>
        <?php endfor; ?>
    </ul>
</div>
<div class="main_page_title">BoardroomBooker</div>
<div class="booker_title"><?php echo $curr_page ?></div>
