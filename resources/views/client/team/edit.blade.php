<form method="POST" action="{{ route('client.team.update', $team) }}" id="form-team-edit">
    @csrf
    @method('PUT')

    <div class="row mx-5 mt-2 mb-2">
        <div class="col">
            <div class="mb-4">
                <label class="form-label" for="team_name"><b>What's your team name</b></label>
                <input type="text" class="form-control gray" name="name" id="team_name" placeholder="Enter your team's name"  value="{{ $team->name }}" required/>
            </div>
        </div>
    </div>
</form>

<script>
    var input = $('#team_name');
    var strLength = input.val().length * 2;
    input.focus();
    input[0].setSelectionRange(strLength, strLength);
</script>
