<label>{$question.question_number}.
{$question.text|wash('xhtml')} {section show=$question.mandatory}<strong class="required">*</strong>{/section}</label>
 
<div class="survey-choices">
    <!-- {$question.answer} -->
    <input type="hidden" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" value="{$question.answer}">
    {if $question.answer|count|gt(0)}
    	{def $net = $question.answer|explode('|')} 
    	Your IP address is {$net.0} {if is_set($net.1)} in network '{$net.1}' {/if}
    {/if}
</div>