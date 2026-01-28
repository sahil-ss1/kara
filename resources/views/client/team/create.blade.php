<form method="POST" action="{{ route('client.team.store') }}" id="form-team-create">
    @csrf
    <div class="row mx-5 mt-2 mb-2">
        <div class="col">
            <div class="mb-4">
                <label class="form-label" for="team_name"><b>What's your team name</b></label>
                <input type="text" class="form-control gray" name="name" id="team_name" placeholder="Enter your team's name" required/>
            </div>
        </div>
    </div>
</form>

<script>
    $('#team_name').focus();
</script>
