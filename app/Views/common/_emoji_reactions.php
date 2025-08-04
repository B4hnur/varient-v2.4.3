<div class="emoji-reactions">
    <?php $array = getReactionsArray();
    foreach ($array as $item) {
        $reVote = 're_' . $item;
        echo view('common/_emoji_reactions_item', ['reactions' => $reactions, 'reactionVote' => $reactions->$reVote, 'reaction' => $item]);
    } ?>
</div>