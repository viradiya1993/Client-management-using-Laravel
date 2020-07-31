<div class="white-box">
    <nav>
        <ul class="showProjectTabs">
            <li class="projects">
                <a href="{{ route('admin.projects.show', $project->id) }}"><span>@lang('modules.projects.overview')</span></a>
            </li>
            @if(in_array('employees',$modules))
                <li class="projectMembers">
                    <a href="{{ route('admin.project-members.show', $project->id) }}"><span>@lang('modules.projects.members')</span></a>
                </li>
            @endif
            <li class="projectMilestones">
                <a href="{{ route('admin.milestones.show', $project->id) }}"><span>@lang('modules.projects.milestones')</span></a>
            </li>
            @if(in_array('tasks',$modules))
                <li class="projectTasks">
                    <a href="{{ route('admin.tasks.show', $project->id) }}"><span>@lang('app.menu.tasks')</span></a>
                </li>
            @endif
            <li class="projectFiles">
                <a href="{{ route('admin.files.show', $project->id) }}"><span>@lang('modules.projects.files')</span></a>
            </li>
            @if(in_array('invoices',$modules))
                <li class="projectInvoices">
                    <a href="{{ route('admin.invoices.show', $project->id) }}"><span>@lang('app.menu.invoices')</span></a>
                </li>
            @endif @if(in_array('timelogs',$modules))
                <li class="projectTimelogs">
                    <a href="{{ route('admin.time-logs.show', $project->id) }}"><span>@lang('app.menu.timeLogs')</span></a>
                </li>
            @endif
            <li class="burndownChart">
                <a href="{{ route('admin.projects.burndown-chart', $project->id) }}"><span>@lang('modules.projects.burndownChart')</span></a>
            </li>
        </ul>
    </nav>
</div>