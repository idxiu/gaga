<div id="all-templates" style="display: none;">

    <input type="hidden" data="<?php echo $session_id;?>" class="session_id" />
    <input type="hidden" data="<?php echo $user_id;?>" class="token" />
    <input type="hidden" data="<?php echo $nickname;?>" class="nickname" />
    <input type="hidden" data="<?php echo $loginName;?>" class="loginName" />
    <input type="hidden" data="<?php echo $avatar;?>" class="self_avatar" />

    <div id="group-invite-people" class="pop-window pop-window-invite-people">
        <div class="pw-left">
            <div class="no_data">
                <div class="d-flex" style="text-align: center">
                    <div class="p-2">
                        <img class="no_data_img" src="../../public/img/no_data.png"/>
                    </div>
                    <div class="p-2 no_data_tip" data-local-value="noFriendDataTip">No Friends For Invite</div>
                </div>
            </div>
        </div>

        <div class="pw-right">
            <div class="pw-right-header justify-content-center" data-local-value="selectContactTip">Selected Contact</div>
            <div class="pw-right-body">

            </div>
            <div class="pw-right-btn">
                <button class="btn-default cancle_invite_people" data-local-value="cancelTip">Cancel</button>
                <button class="btn-primary add_member_to_group" data-local-value="addTip">Add</button>
            </div>
        </div>
    </div>

    <div id="group-remove-people" class="pop-window pop-window-remove-people">
        <div class="pw-right-header" style="justify-content: center" data-local-value="removeTip" >Select Member For Remove</div>

        <div class="remove-people-div" style="width: 100%;">
        </div>
        <div style="text-align: center;">
            <button class="btn-primary remove_member_from_group" style="font-size:1.67rem;" data-local-value="removeGroupMemberTip">Remove Member</button>
        </div>
    </div>


    <div id="create-group">

        <div class="flex-container justify-content-center" >
            <div class="header_tip_font align-items-center"  data-local-value="createGroupTip">Create Group</div>
        </div>

        <div class="d-flex flex-row justify-content-center">
            <input type="text" class="form-control group_name create_group_box_div_input"  data-local-placeholder="enterGroupNamePlaceholder" placeholder="Please Enter Group Name" >
        </div>

        <div class="line"></div>

        <div  class="d-flex flex-row justify-content-center  data_tip" data-local-value="createGroupNameTip">
            The Length Of Group Name between 1 and 20
        </div>

        <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
            <button type="button" class="btn create_button create_group_button" data-local-value="createTip">Create</button>
        </div>
    </div>

    <div id="edit-remark">
        <div class="flex-container" style="display: flex;justify-content: center;">
            <div class="header_tip_font align-items-center" data-local-value="editRemarkTip">Edit Remark</div>
        </div>

        <div class="d-flex flex-row justify-content-center" style="">
            <input type="text" class="form-control remark_name create_group_box_div_input "  data-local-placeholder="remarkNamePlaceholder"  placeholder="Please Enter Remark Name" >
        </div>

        <div class="line"></div>


        <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
            <button type="button" class="btn create_button edit_remark_for_friend" data-local-value="sureTip">Sure</button>
        </div>
    </div>

    <div id="add-friend-div">
        <div class="flex-container justify-content-center" >
            <div class="header_tip_font  align-items-center" style="margin-top: 6rem;" data-local-value="addFriendTip">Add Friend</div>
        </div>

        <div class="d-flex flex-row justify-content-center" style="margin-top: 5rem; text-align: center;position: relative" >
            <img  class="user-image-for-add" src="../../public/img/msg/default_user.png" style="width: 8rem; height: 8rem;" />
        </div>
        <div class="d-flex flex-row justify-content-center user-nickname-for-add" style="text-align: center;position: relative;font-size:1.31rem;font-family:PingFangSC-Regular;color:rgba(20,16,48,1);" >

        </div>

        <div class="d-flex flex-row justify-content-center" >
            <input type="text" class="form-control  create_group_box_div_input apply-friend-reason" data-local-placeholder="addFriendReasonPlaceholder"  placeholder="Please Enter Introduce" >
        </div>

        <div class="line"></div>

        <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
            <button type="button" class="btn create_button apply-friend" data-local-value="sendTip">Send</button>
        </div>
    </div>


    <div id="permission-join">
        <div class="flex-container" style="display: flex;justify-content: center;">
            <div class="header_tip_line_left "></div>
            <div class="header_tip_font  font-size-12  align-items-center" data-local-value="joinGroupPermissionsTip">Add Group Permissions</div>
            <div class="header_tip_line_right"></div>
        </div>

        <div class="d-flex flex-row" style="margin-top: 5rem; margin-left:23rem;">
            <div class="permission-join-operation join-by-admin">
                <div class="d-flex flex-row" style="width: 50%"  data-local-value="groupAdminTip">Administrator Invitation</div>
                <div  class="d-flex flex-row ">
                    <img class="imgDiv" src="../../public/img/msg/member_unselect.png"  permissionJoin="GroupJoinPermissionAdmin">
                </div>
            </div>
            <div class="permission-join-operation join-by-member">
                <div class="d-flex flex-row" style="width: 50%" data-local-value="groupMemberTip">
                    Members Invitation
                </div>
                <div class="d-flex flex-row ">
                    <img class="imgDiv"src="../../public/img/msg/member_unselect.png" permissionJoin="GroupJoinPermissionMember">
                </div>
            </div>
            <div class="permission-join-operation join-by-public">
                <div class="d-flex flex-row" style="width: 50%" data-local-value="groupPublicTip"> Public</div>
                <div class="d-flex flex-row ">
                    <img class="imgDiv" src="../../public/img/msg/member_unselect.png" permissionJoin="GroupJoinPermissionPublic">
                </div>
            </div>
        </div>

        <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
            <button type="button" class="btn create_button save-permission-join" style="margin-bottom: 5rem;" data-local-value="saveTip">Save</button>
        </div>
    </div>

    <div id="share_group" >
        <div class="" style="width: 19rem;margin: 0 auto;margin-top: 5rem; ">
            <div style="display: flex;margin-bottom: 3rem;   justify-content: center;">
                <div class="header" style="width: 5rem;height: 5rem;margin-right: 1rem">
                    <img class="group_avatar" src="../../public/img/msg/group_default_avatar.png" style="width: 5rem;height: 5rem;">
                </div>
                <div class="name" style="margin-top: 1rem;">
                    <span style="font-size:1.69rem;font-family:PingFangSC-Regular;color:rgba(20,16,48,1);"> </span>
                    <span style="font-size:1.31rem;font-family:PingFangSC-Regular;color:rgba(153,153,153,1);"> </span>
                </div>
            </div>
            <div id="qrcodeCanvas" style="width:19rem; height: 19rem;">
            </div>
        </div>

        <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
            <button type="button" class="btn create_button copy-share-group" style="margin-bottom: 5rem;" data-local-value="copyGroupQrcodeUrlTip">Copy Group Url</button>
            <button type="button" class="btn create_button save-share-group" style="margin-bottom: 5rem;" data-local-value="saveGroupQrcodeImg">Save Qrcode</button>
        </div>
    </div>
</div>

