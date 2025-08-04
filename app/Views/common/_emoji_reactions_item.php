<?php $sessVoteCount = 0;
$varSess = getSession('reaction_total_votes_' . $reactions->post_id);
if (empty($varSess)) {
    $varSess = helperGetCookie('reaction_total_votes_' . $reactions->post_id);
}
if (!empty($varSess)) {
    $sessVoteCount = intval($varSess);
}
$isVoted = false;
if (!empty(isReactionVoted($reactions->post_id, $reaction))) {
    $isVoted = true;
}
if (!empty($resultArray) && $resultArray['reaction'] == $reaction) {
    if ($resultArray['operation'] == 'increase') {
        $isVoted = true;
    }
    if ($resultArray['operation'] == 'decrease') {
        $isVoted = false;
    }
}
$isDisabled = false;
$classes = '';
if ($isVoted) {
    $classes = ' selected';
}
if (!$isVoted && $sessVoteCount >= 3) {
    $isDisabled = true;
    $classes .= ' disabled';
} ?>

<div class="reaction-container">
    <div class="reaction<?= $classes; ?>" <?= $isDisabled ? '' : "onclick=\"addReaction('$post->id', '$reaction');\"" ?>>
        <img src="<?= base_url('assets/img/reactions/' . $reaction . '.png'); ?>" alt="<?= trans($reaction); ?>" class="emoji">
        <span class="text"><?= trans($reaction); ?></span>
        <span class="vote"><?= $reactionVote; ?></span>
    </div>
</div>