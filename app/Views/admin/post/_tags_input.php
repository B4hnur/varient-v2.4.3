<link rel="stylesheet" href="<?= base_url("assets/admin/plugins/tagify/tagify.css") ?>">
<script src="<?= base_url("assets/admin/plugins/tagify/tagify.js") ?>"></script>

<div class="row">
    <div class="col-sm-12 m-b-5">
        <label class="control-label"><?= trans('tags'); ?></label>
        <a href="<?= adminUrl("tags"); ?>" class="btn btn-xs btn-default btn-manage-tags" target="_blank"><i class="fa fa-edit"></i>&nbsp;<?= trans("manage_tags"); ?></a>
    </div>
    <div class="col-sm-12">
        <input name="tags" id="tag-input" class="form-control tags-input" value="<?= !empty($tags) ? $tags : ''; ?>" placeholder="<?= clrDQuotes(trans('type_tag')); ?>"/>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputElement = document.querySelector('#tag-input');
        const tagify = new Tagify(inputElement, {
            enforceWhitelist: false,
            whitelist: [],
            maxTags: <?= POST_TAGS_LIMIT; ?>,
            dropdown: {
                enabled: 1,
                position: 'text',
                closeOnSelect: false
            }
        });
        tagify.on('input', function (e) {
            const searchTerm = e.detail.value;
            if (searchTerm.length < 2) return;
            var data = {
                searchTerm: searchTerm
            };
            $.ajax({
                type: 'POST',
                url: VrConfig.baseURL + '/Ajax/getTagSuggestions',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        tagify.settings.whitelist = obj.tags;
                        tagify.dropdown.show(e.detail.value);
                    }
                }
            });
        });
    });
</script>