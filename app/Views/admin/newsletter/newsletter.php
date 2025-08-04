<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('newsletter'); ?></h3>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('users'); ?>&nbsp;(<?= $usersCount; ?>)</h3>
                </div>
                <div class="right">
                    <input type="text" id="searchUsers" class="form-control" placeholder="<?= trans("search"); ?>" style="width: 180px;">
                </div>
            </div>
            <div class="box-body">
                <div id="userTableContainer" class="tableFixHead">
                    <table class="table table-users">
                        <thead>
                        <tr>
                            <th width="20"><input type="checkbox" id="checkboxAllUsers"></th>
                            <th><?= trans("id"); ?></th>
                            <th><?= trans("username"); ?></th>
                            <th><?= trans("email"); ?></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <?php if ($usersCount > 0): ?>
                        <div id="spinnerUsers">
                            <div class="spinner" style="margin-top: 15px;">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box-footer">
                <form action="<?= adminUrl('newsletter-send-email'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="user_ids" id="selectedUserIds">
                    <button type="submit" name="submit" value="users" class="btn btn-lg btn-block btn-info"><?= trans("send_email"); ?>&nbsp;&nbsp;<i class="fa fa-send"></i></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('subscribers'); ?>&nbsp;(<?= $subscribersCount; ?>)</h3>
                </div>
                <div class="right">
                    <input type="text" id="searchSubscribers" class="form-control" placeholder="<?= trans("search"); ?>" style="width: 180px;">
                </div>
            </div>
            <div class="box-body">
                <div id="subscriberTableContainer" class="tableFixHead">
                    <table class="table table-subscribers">
                        <thead>
                        <tr>
                            <th width="20"><input type="checkbox" id="checkboxAllSubscribers"></th>
                            <th><?= trans("id"); ?></th>
                            <th><?= trans("email"); ?></th>
                            <th><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <?php if ($subscribersCount > 0): ?>
                        <div id="spinnerSubscribers">
                            <div class="spinner" style="margin-top: 15px;">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box-footer">
                <form action="<?= adminUrl('newsletter-send-email'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="subscriber_ids" id="selectedSubscriberIds">
                    <button type="submit" name="submit" value="subscribers" class="btn btn-lg btn-block btn-info"><?= trans("send_email"); ?>&nbsp;&nbsp;<i class="fa fa-send"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('settings'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/newsletterSettingsPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('newsletter_status', 1, 0, trans("enable"), trans("disable"), $generalSettings->newsletter_status); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("newsletter_popup"); ?></label>
                        <?= formRadio('newsletter_popup', 1, 0, trans("enable"), trans("disable"), $generalSettings->newsletter_popup); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("image"); ?></label>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= getNewsletterImage(); ?>" alt="" style="max-width: 300px; max-height: 300px;">
                        </div>
                        <div class="display-block">
                            <a class='btn btn-success btn-sm btn-file-upload'>
                                <?= trans('select_image'); ?>
                                <input type="file" name="file" size="40" accept=".jpg, .jpeg, .webp, .png" onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));">
                            </a>
                            (.jpg, .jpeg, .webp, .png)
                        </div>
                        <span class='label label-info' id="upload-file-info"></span>
                    </div>
                </div>
                <div class="box-footer text-right">
                    <button type="submit" name="submit" value="general" class="btn btn-primary"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $("#checkboxAllUsers").click(function () {
        $('.table-users input:checkbox').not(this).prop('checked', this.checked);
        updateSelectedUserIds();
    });
    $("#checkboxAllSubscribers").click(function () {
        $('.table-subscribers input:checkbox').not(this).prop('checked', this.checked);
        updateSelectedSubscriberIds();
    });

    $(document).on('change', 'input[name="user_id[]"]', function () {
        updateSelectedUserIds();
    });
    $(document).on('change', 'input[name="subscriber_id[]"]', function () {
        updateSelectedSubscriberIds();
    });

    function updateSelectedUserIds() {
        var selectedValues = $('input[name="user_id[]"]:checked').map(function () {
            return $(this).val();
        }).get();
        $('#selectedUserIds').val(selectedValues.join(','));
    }

    function updateSelectedSubscriberIds() {
        var selectedValues = $('input[name="subscriber_id[]"]:checked').map(function () {
            return $(this).val();
        }).get();
        $('#selectedSubscriberIds').val(selectedValues.join(','));
    }
</script>
<style>
    .tableFixHead {
        overflow: auto;
        height: 600px !important;
    }

    .tableFixHead thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 8px 16px;
    }

    th {
        background: #fff !important;
    }

    .spinner > div {
        background-color: #BDBDBD;
    }
</style>

<script>
    //Load More Users
    let usersCurrentPage = 0;
    let usersSearchQuery = '';
    let isUsersLoading = false;
    let debounceTimerUsers;
    document.getElementById("userTableContainer").addEventListener("scroll", function () {
        const container = this;
        if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10 && !isUsersLoading) {
            loadMoreUsers();
        }
    });

    function loadMoreUsers() {
        $('#spinnerUsers').show();
        isUsersLoading = true;
        usersCurrentPage++;
        var data = {
            'page': usersCurrentPage,
            'q': usersSearchQuery
        };
        $.ajax({
            type: 'POST',
            url: VrConfig.baseURL + '/Ajax/loadMoreUsers',
            data: setAjaxData(data),
            success: function (response) {
                setTimeout(function () {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        $("#userTableContainer tbody").append(obj.htmlContent);
                    }
                    isUsersLoading = false;
                    $('#spinnerUsers').hide();
                }, 500);
            },
            error: function (response) {
                $('#spinnerUsers').hide();
            }
        });
    }

    $(document).on('input', '#searchUsers', function () {
        clearTimeout(debounceTimerUsers);
        var $input = $(this);
        debounceTimerUsers = setTimeout(function () {
            var q = $input.val().trim();
            if (q && q.length > 1) {
                usersSearchQuery = q;
            } else {
                usersSearchQuery = '';
            }
            usersCurrentPage = 0;
            $("#userTableContainer tbody").empty();
            loadMoreUsers();
        }, 300);
    });

    //Load More Subscribers
    let subscribersCurrentPage = 0;
    let subscribersSearchQuery = '';
    let isSubscribersLoading = false;
    let debounceTimerSubscribers;
    document.getElementById("subscriberTableContainer").addEventListener("scroll", function () {
        const container = this;
        if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10 && !isSubscribersLoading) {
            loadMoreSubscribers();
        }
    });

    function loadMoreSubscribers() {
        $('#spinnerSubscribers').show();
        isSubscribersLoading = true;
        subscribersCurrentPage++;
        var data = {
            'page': subscribersCurrentPage,
            'q': subscribersSearchQuery
        };
        $.ajax({
            type: 'POST',
            url: VrConfig.baseURL + '/Ajax/loadMoreSubscribers',
            data: setAjaxData(data),
            success: function (response) {
                setTimeout(function () {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        $("#subscriberTableContainer tbody").append(obj.htmlContent);
                    }
                    isSubscribersLoading = false;
                    $('#spinnerSubscribers').hide();
                }, 400);
            },
            error: function (response) {
                $('#spinnerSubscribers').hide();
            }
        });
    }

    $(document).on('input', '#searchSubscribers', function () {
        clearTimeout(debounceTimerSubscribers);
        var $input = $(this);
        debounceTimerSubscribers = setTimeout(function () {
            var q = $input.val().trim();
            if (q && q.length > 1) {
                subscribersSearchQuery = q;
            } else {
                subscribersSearchQuery = '';
            }
            subscribersCurrentPage = 0;
            $("#subscriberTableContainer tbody").empty();
            loadMoreSubscribers();
        }, 300);
    });

    $(document).ready(function () {
        loadMoreUsers();
        loadMoreSubscribers();
    });
</script>