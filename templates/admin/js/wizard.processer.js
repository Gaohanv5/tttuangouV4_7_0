/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name wizard.processer.js * @date 2014-10-30 10:42:14 */ function wizProcessStart()
{
    $('#nav2Base').trigger('click');
    $__wizDSP = 0;
    wizProcess();
}
var $__wizDialog = null;
var $__wizAll = [
    ['productName', '首先，您需要填入您的产品名称！', 'bottom'],
    ['productFlag', '然后，请为您的产品起一个简短的名称！', 'bottom'],
    ['fillIfoCity', '在这里选择产品的投放城市，如果列表中不存在，您可以点击[添加城市]新建一个', 'bottom'],
    ['fillIfoSeller', '这里显示的是目标投放城市中的所有商家，如果查询不到，您可以点击[添加商家]新建一个', 'bottom'],
    ['fillIfoDisplay', '发布的时候不要忘记设置这个哦，否则前台会看不到产品的！', 'bottom'],
	['nav2Type', '点击这里可以切换到产品属性设置页面（产品价格、时间、购买限制等）', 'bottom'],
    ['productPrice', '请在这里输入产品的市场价格（原价）', 'bottom'],
    ['productNowPrice', '在这里输入产品的{TUANGOU_STR}价格（现价）', 'bottom'],
    ['product_type_sel', '这里可以选择{TUANGOU_STR}的类型；<br/>{TUANGOU_STR}券为虚拟{TUANGOU_STR}，成团后会向用户下发一串12位的数字和6位的密码，用以去商家处消费；<br/>实物的话成团后，管理员可以在后台看到卖家收货地址，然后进行发货操作', 'bottom'],
    ['nav2Intro', '点击这里可以切换到产品详情信息编辑页面', 'bottom'],
    ['nav2Extend', '点击这里可以看到一些扩展，比如是否允许多次购买同一产品，或者是否在产品详情页面显示商家地图信息', 'bottom'],
	//  ['nav2Image', '点击这里可以切换到产品图片上传页面', 'bottom'],

];
var $__wizDSP = 0;
function wizProcess()
{
    var nowLast = $__wizAll.length == $__wizDSP+1;
    var id = $__wizAll[$__wizDSP][0];
    var text = $__wizAll[$__wizDSP][1];
    var pos = $__wizAll[$__wizDSP][2];
    var isNav = /^nav2/.test(id);
    var tar = $('#'+id);
    // set target layer z-index
    tar.css({'position':'relative','z-index':20001});
    isNav || tar.css({'background':'#fff'});
    // pos now support [right,bottom]
    var arrowIcon = pos == 'right' ? 'arrow-left' : 'arrow-top';
    var options = {
        lock: true,
        resize: false,
        drag: false,
        title: false,
        opacity: 0.5,
        icon: arrowIcon,
        content: '<div id="__wiz_Dialog_TXT__">'+text+'</div>',
        yesText: nowLast ? '知道了' : '下一步',
        yesFn: function(){
            // nav Click
            isNav && tar.trigger('click');
            // restore style
            tar.css({'position':'','z-index':1});
            isNav || tar.css({'background':'none'});
            // show Next
            if (!nowLast)
            {
                $__wizDSP ++;
                wizProcess();
            }
        }
    };
    $__wizDialog = art.dialog(options);
    // reset dialog position
    var wiz = $('#__wiz_Dialog_TXT__');
    var relTop = tar.offset().top;
    var relLeft = tar.offset().left;
    var docScrollTop = $(document).scrollTop();
    if (pos == 'bottom')
    {
        relTop += (tar.height()+wiz.height());
        // if Scrolled
        relTop -= docScrollTop > 0 ? docScrollTop : 0;
        // get Left
        relLeft -= 10;
    }
    else
    {
        relLeft += tar.width();
        relTop -= 30;
    }
    $__wizDialog.position(relLeft, relTop);
}