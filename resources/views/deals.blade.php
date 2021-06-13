@extends('app')

@section('content')

    <!-- Bootstrap Boilerplate... -->

    <div class="panel-body">
        <!-- New Deal Form -->
        <form action="/deal" method="POST" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="deal" class="control-label">Deal</label>
                <div >
                    <input type="text" name="Deal_Name" id="Deal_Name" class="form-control" placeholder="Deal Name">
                </div>
                <div >
                    <input type="text" name="Ammount" id="Ammount" class="form-control" placeholder="Ammount">
                </div>
                <div >
                    <input type="text" name="Expected_Revenue" id="Expected_Revenue" class="form-control" placeholder="Expected Revenue">
                </div>
                <div >
                    {{-- Linked Task creation checkbox --}}
                    <input type="checkbox" name="create_task" id="create_task" class="form-control" placeholder=" Create Task">
                    <label for="create_task">Create Task</label>
                </div>
                <div >
                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-plus"></i> Add Deal
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection