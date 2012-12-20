<label>{$question.question_number}.
{$question.text|wash('xhtml')} {section show=$question.mandatory}<strong class="required">*</strong>{/section}</label>
 
<div class="survey-choices">
        <!-- {$question.answer} -->
        {if $question.answer|count|gt(0)}
                <input type="hidden" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" value="{$question.answer}">
        {/if}
</div>