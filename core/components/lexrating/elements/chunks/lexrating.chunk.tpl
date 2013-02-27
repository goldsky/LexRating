
<div class="lexrating-wrapper">
    [[+lexrating.name]]
    <div
        class="rateit"
        id="[[+lexrating.group]]_[[+lexrating.name]]"
        data-objectid="[[+lexrating.id]]"
        data-rateit-value="[[+lexrating.value]]"
        data-rateit-extended="[[+lexrating.extended]]"
        data-rateit-readonly="[[+lexrating.allowedToVote:is=`1`:then=`false`:else=`true`]]"
        ></div>
    (<span id="count_[[+lexrating.group]]_[[+lexrating.name]]">[[+lexrating.total.voters]]</span> voters)
    [[+lexrating.initialAjax:is=`1`:then=`<script>
        getRating('[[+lexrating.id]]', '#[[+lexrating.group]]_[[+lexrating.name]]');
    </script>`:else=``]]
</div>
