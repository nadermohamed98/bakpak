<!-- Add a button to trigger the popup -->
<button id="selectCourseBtn" class="btn btn-primary">{{ __('Select Course') }}</button>

<!-- The popup modal -->
<div id="courseModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Select Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Course list will be loaded here -->
                <ul id="courseList" class="list-group">
                    <!-- Courses will be dynamically added here -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
