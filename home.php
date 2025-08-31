<div class="py-4">
    <h3 class="text-center">Welcome to E-Vote Nation</h3>
    <hr>
</div>
<?php
$is_vote = $conn->query("SELECT count(vote_id) as `count` FROM `vote_list` where election_id = '{$_SESSION['election']['election_id']}' and voter_id = '{$_SESSION['voter_id']}' ")->fetchArray()[0];
?>
<div class="py-5 w-100 d-flex justify-content-center align-items-cente">
    <?php if($is_vote <= 0): ?>
    <a href="./?page=vote" class="btn btn-primary btn-lg rounded-pill col-md-4">Vote Now</a>
    <?php else: ?>
    <a href="./?page=ballot_preview" class="btn btn-primary btn-lg rounded-pill col-md-4">View Submitted Ballot</a>
    <?php endif; ?>
</div>

