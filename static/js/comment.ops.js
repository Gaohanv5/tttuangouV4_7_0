/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name comment.ops.js * @date 2013-08-20 15:04:17 */ $(document).ready(function(){
	if ($('#comment-form').length > 0)
	{
		comment_form_init();
	}
	$('#comment-button').bind('click', comment_form_submit);
});

function comment_form_init()
{
	var spans = '';
	for (var i = 1; i <= 5; i++)
	{
		spans += '<span class="comment-score-block-span" onmouseover="comment_score_over('+i+')"></span>';
	}
	$('#comment-score-selector').html(spans);
	comment_score_over(5);
}

function comment_score_over(i)
{
	$('#comment-score-displayer').css('width', (i * 20).toString()+'%');
	$('#i-comment-score').val(i);
}

function comment_form_submit()
{
	var url = $('#comment-form').attr('action');
	var hash = $('#comment-form input[name=FORMHASH]').val();
	var score = $('#i-comment-score').val();
	var content = $('#i-comment-content').val();
	// submitting
	$('#comment-form').hide();
	comment_form_loading('正在提交中，请稍候...');
	$.post(url, {'FORMHASH' : hash, 'score' : score, 'content' : content}, function(result){
		if (result == 'ok')
		{
			comment_form_loading('评价已提交！', 'success');
		}
		else
		{
			comment_form_loading(result, 'error');
		}
	});
}

function comment_form_loading(content, style)
{
	var domClass = style ? style : 'loading';
	$('#comment-form-loading').attr('class', 'comment-form-'+style).show().html(content);
}