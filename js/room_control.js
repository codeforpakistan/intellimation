/**
 * Created by Saira Saeed on 2/22/15.
 */
function OnDevice(roomane,deviceid,devicetype)
{
   $('#'+roomane+"_"+deviceid).removeClass('active');
    var OffHTML = GetOffHTML(roomane,deviceid,devicetype);
   $('#'+roomane+"_"+deviceid+"_onoff").html(OffHTML);
}

function OffDevice(roomane,deviceid,devicetype)
{
    $('#'+roomane+"_"+deviceid).addClass('active');
    var ONHTML = GetOnHTML(roomane,deviceid,devicetype);
    $('#'+roomane+"_"+deviceid+"_onoff").html(ONHTML);
}

function GetOffHTML(roomane,deviceid,devicetype)
{
    var OffHTML = '<a onclick="javascript:OffDevice(\''+roomane+'\', '+deviceid+',\''+devicetype+'\')"><i class="fa fa-circle-o"></i> OFF</a>';
    return OffHTML;
}

function GetOnHTML(roomane,deviceid,devicetype)
{
    var ONHTML = '<a onclick="javascript:OnDevice(\''+roomane+'\','+deviceid+',\''+devicetype+'\')"><i class="fa fa-dot-circle-o"></i> ON</a>';
    return ONHTML;
}