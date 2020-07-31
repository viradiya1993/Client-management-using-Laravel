<div class="navbar-default sidebar" role="navigation">
    <div class="navbar-header">
        <!-- Toggle icon for mobile view -->
        <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse"
            data-target=".navbar-collapse"><i class="ti-menu"></i></a>

        <div class="top-left-part">
            <!-- Logo -->
            <a class="logo hidden-xs text-center" href="{{ route('admin.dashboard') }}">
                <span class="visible-md"><img src="{{ $global->logo_url }}" alt="home" class=" admin-logo"/></span>
                <span class="visible-sm"><img src="{{ $global->logo_url }}" alt="home" class=" admin-logo"/></span>
            </a>

        </div>
        <!-- /Logo -->

        <!-- This is the message dropdown -->
        <ul class="nav navbar-top-links navbar-right pull-right visible-xs">
            @if(isset($activeTimerCount))
            <li class="dropdown hidden-xs">
            <span id="timer-section">
                <div class="nav navbar-top-links navbar-right pull-right m-t-10">
                    <a class="btn btn-rounded btn-default timer-modal" href="javascript:;">@lang("modules.projects.activeTimers")
                        <span class="label label-danger" id="activeCurrentTimerCount">@if($activeTimerCount > 0) {{ $activeTimerCount }} @else 0 @endif</span>
                    </a>
                </div>
            </span>
            </li>
            @endif


            <li class="dropdown">
                <select class="selectpicker language-switcher" data-width="fit">
                    <option value="en" @if($global->locale == "en") selected @endif data-content='<span class="flag-icon flag-icon-us"></span> En'>En</option>
                    @foreach($languageSettings as $language)
                        <option value="{{ $language->language_code }}" @if($global->locale == $language->language_code) selected @endif  data-content='<span class="flag-icon flag-icon-{{ $language->language_code }}"></span> {{ $language->language_code }}'>{{ $language->language_code }}</option>
                    @endforeach
                </select>
            </li>

            <!-- .Task dropdown -->
            <li class="dropdown" id="top-notification-dropdown">
                <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#">
                    <i class="icon-bell"></i>
                    @if(count($user->unreadNotifications) > 0)
                        <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
                    @endif
                </a>
                <ul class="dropdown-menu  dropdown-menu-right mailbox animated slideInDown">
                    <li>
                        <div class="drop-title">@lang('app.newNotifications') <span
                                    class="top-notification-count">{{ count($user->unreadNotifications) }}</span>
                        </div>
                    </li>
                    @foreach ($user->unreadNotifications as $notification)
                        @if(view()->exists('notifications.member.'.\Illuminate\Support\Str::snake(class_basename($notification->type))))
                            @include('notifications.member.'.\Illuminate\Support\Str::snake(class_basename($notification->type)))
                        @endif
                    @endforeach

                    @if(count($user->unreadNotifications) > 0)
                        <li>
                            <a class="text-center mark-notification-read"
                                href="javascript:;"> @lang('app.markRead') <i class="fa fa-check"></i> </a>
                        </li>
                    @endif
                </ul>
            </li>
            <!-- /.Task dropdown -->


            <li class="dropdown">
                <a href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();"
                ><i class="fa fa-power-off"></i>
                </a>
            </li>



        </ul>

    </div>
    <!-- /.navbar-header -->

    <div class="top-left-part">
        <a class="logo hidden-xs hidden-sm text-center" href="{{ route('admin.dashboard') }}">
            <img src="{{ $global->logo_url }}" alt="home" class=" admin-logo"/>
        </a>
    </div>
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">

        <!-- .User Profile -->
        <ul class="nav" id="side-menu">
            <li class="sidebar-search hidden-sm hidden-md hidden-lg">
                <!-- input-group -->
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search..."> <span class="input-group-btn">
                            <button class="btn btn-default" type="button"> <i class="fa fa-search"></i> </button>
                            </span> </div>
                <!-- /input-group -->
            </li>

            <li class="user-pro hidden-sm hidden-md hidden-lg">
                @if(is_null($user->image))
                    <a href="#" class="waves-effect"><img src="{{ asset('img/default-profile-3.png') }}" alt="user-img" class="img-circle"> <span class="hide-menu">{{ (strlen($user->name) > 24) ? substr(ucwords($user->name), 0, 20).'..' : ucwords($user->name) }}
                    <span class="fa arrow"></span></span>
                    </a>
                @else
                    <a href="#" class="waves-effect"><img src="{{ asset_url('avatar/'.$user->image) }}" alt="user-img" class="img-circle"> <span class="hide-menu">{{ ucwords($user->name) }}
                            <span class="fa arrow"></span></span>
                    </a>
                @endif
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ route('member.dashboard') }}">
                            <i class="fa fa-sign-in"></i> @lang('app.loginAsEmployee')
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();"
                        ><i class="fa fa-power-off"></i> @lang('app.logout')</a>

                    </li>
                </ul>
            </li>

            <li><a href="{{ route('admin.dashboard') }}" class="waves-effect"><i class="icon-speedometer"></i> <span class="hide-menu">@lang('app.menu.dashboard') </span></a> </li>

            @if(in_array('clients',$modules) || in_array('leads',$modules))
                <li><a href="{{ route('admin.clients.index') }}" class="waves-effect"><i class="icon-people"></i> <span class="hide-menu"> @lang('app.menu.customers') <span class="fa arrow"></span> </span></a>
                    <ul class="nav nav-second-level">
                        @if(in_array('clients',$modules))
                            <li><a href="{{ route('admin.clients.index') }}" class="waves-effect">@lang('app.menu.clients')</a> </li>
                        @endif
                        @if(in_array('leads',$modules))
                            <li><a href="{{ route('admin.leads.index') }}" class="waves-effect">@lang('app.menu.lead')</a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(in_array('employees', $modules) || in_array('attendance', $modules) || in_array('holidays', $modules) || in_array('leaves', $modules))
                <li><a href="{{ route('admin.employees.index') }}" class="waves-effect"><i class="ti-user"></i> <span class="hide-menu"> @lang('app.menu.hr') <span class="fa arrow"></span> </span></a>
                    <ul class="nav nav-second-level">
                        @if(in_array('employees',$modules))
                            <li><a href="{{ route('admin.employees.index') }}">@lang('app.menu.employeeList')</a></li>
                            <li><a href="{{ route('admin.teams.index') }}">@lang('app.department')</a></li>
                            <li><a href="{{ route('admin.designations.index') }}">@lang('app.menu.designation')</a></li>
                        @endif
                        @if(in_array('attendance',$modules))
                            <li><a href="{{ route('admin.attendances.summary') }}" class="waves-effect">@lang('app.menu.attendance')</a> </li>
                        @endif
                        @if(in_array('holidays',$modules))
                            <li><a href="{{ route('admin.holidays.index') }}" class="waves-effect">@lang('app.menu.holiday')</a>
                            </li>
                        @endif
                        @if(in_array('leaves',$modules))
                            <li><a href="{{ route('admin.leaves.pending') }}" class="waves-effect">@lang('app.menu.leaves')</a> </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(in_array('projects', $modules) || in_array('tasks', $modules) || in_array('timelogs', $modules) || in_array('contracts', $modules))
                <li><a href="{{ route('admin.task.index') }}" class="waves-effect"><i class="icon-layers"></i> <span class="hide-menu"> @lang('app.menu.work') <span class="fa arrow"></span> </span></a>
                    <ul class="nav nav-second-level">
                        @if(in_array('contracts', $modules))
                            <li><a href="{{ route('admin.contracts.index') }}" class="waves-effect">@lang('app.menu.contracts')</a></li>
                        @endif
                        @if(in_array('projects',$modules))
                            <li><a href="{{ route('admin.projects.index') }}" class="waves-effect">@lang('app.menu.projects') </a> </li>
                        @endif
                        @if(in_array('tasks',$modules))
                            <li><a href="{{ route('admin.all-tasks.index') }}">@lang('app.menu.tasks')</a></li>
                            <li class="hidden-sm hidden-xs"><a href="{{ route('admin.taskboard.index') }}">@lang('modules.tasks.taskBoard')</a></li>
                            <li><a href="{{ route('admin.task-calendar.index') }}">@lang('app.menu.taskCalendar')</a></li>
                        @endif
                        @if(in_array('timelogs',$modules))
                            <li><a href="{{ route('admin.all-time-logs.index') }}" class="waves-effect">@lang('app.menu.timeLogs')</a> </li>
                        @endif

                    </ul>
                </li>
            @endif

            @if((in_array("estimates", $modules)  || in_array("invoices", $modules)  || in_array("payments", $modules) || in_array("expenses", $modules)  ))
                <li><a href="{{ route('admin.finance.index') }}" class="waves-effect"><i class="fa fa-money"></i> <span class="hide-menu"> @lang('app.menu.finance') @if($unreadExpenseCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif <span class="fa arrow"></span> </span></a>
                    <ul class="nav nav-second-level">
                        @if(in_array("estimates", $modules))
                            <li><a href="{{ route('admin.estimates.index') }}">@lang('app.menu.estimates')</a> </li>
                        @endif

                        @if(in_array("invoices", $modules))
                            <li><a href="{{ route('admin.all-invoices.index') }}">@lang('app.menu.invoices')</a> </li>
                        @endif

                        @if(in_array("payments", $modules))
                            <li><a href="{{ route('admin.payments.index') }}">@lang('app.menu.payments')</a> </li>
                        @endif

                        @if(in_array("expenses", $modules))
                            <li><a href="{{ route('admin.expenses.index') }}">@lang('app.menu.expenses') @if($unreadExpenseCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif</a> </li>
                        @endif

                        @if(in_array("invoices", $modules))
                            <li><a href="{{ route('admin.all-credit-notes.index') }}">@lang('app.menu.credit-note')</a> </li>
                        @endif
                    </ul>
                </li>
            @endif


            @if(in_array("products", $modules))
                <li><a href="{{ route('admin.products.index') }}" class="waves-effect"><i class="icon-basket"></i> <span class="hide-menu">@lang('app.menu.products') </span></a> </li>
            @endif

            @if(in_array("tickets", $modules))
                <li><a href="{{ route('admin.tickets.index') }}" class="waves-effect"><i class="ti-ticket"></i> <span class="hide-menu">@lang('app.menu.tickets')</span> @if($unreadTicketCount > 0) <div class="notify notification-color"><span class="heartbit"></span><span class="point"></span></div>@endif</a> </li>
            @endif


            @if(in_array("messages", $modules))
                <li><a href="{{ route('admin.user-chat.index') }}" class="waves-effect"><i class="icon-envelope"></i> <span class="hide-menu">@lang('app.menu.messages') @if($unreadMessageCount > 0)<span class="label label-rouded label-custom pull-right">{{ $unreadMessageCount }}</span> @endif</span></a> </li>
            @endif

            @if(in_array("events", $modules))
                <li><a href="{{ route('admin.events.index') }}" class="waves-effect"><i class="icon-calender"></i> <span class="hide-menu">@lang('app.menu.Events')</span></a> </li>
            @endif

            @if(in_array("notices", $modules))
                <li><a href="{{ route('admin.notices.index') }}" class="waves-effect"><i class="ti-layout-media-overlay"></i> <span class="hide-menu">@lang('app.menu.noticeBoard') </span></a> </li>
            @endif
            @if(in_array("reports", $modules))
            <li><a href="{{ route('admin.reports.index') }}" class="waves-effect"><i class="ti-pie-chart"></i> <span class="hide-menu"> @lang('app.menu.reports') <span class="fa arrow"></span> </span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('admin.task-report.index') }}">@lang('app.menu.taskReport')</a></li>
                    <li><a href="{{ route('admin.time-log-report.index') }}">@lang('app.menu.timeLogReport')</a></li>
                    <li><a href="{{ route('admin.finance-report.index') }}">@lang('app.menu.financeReport')</a></li>
                    <li><a href="{{ route('admin.income-expense-report.index') }}">@lang('app.menu.incomeVsExpenseReport')</a></li>
                    <li><a href="{{ route('admin.leave-report.index') }}">@lang('app.menu.leaveReport')</a></li>
                    <li><a href="{{ route('admin.attendance-report.index') }}">@lang('app.menu.attendanceReport')</a></li>
                </ul>
            </li>
            @endif

            @role('admin')
            <li><a href="{{ route('admin.billing') }}" class="waves-effect"><i class="icon-book-open"></i> <span class="hide-menu"> @lang('app.menu.billing')</span></a>
            </li>
            @endrole

            @foreach ($worksuitePlugins as $item)
                @if(in_array(strtolower($item), $modules))
                    @if(View::exists(strtolower($item).'::sections.left_sidebar'))
                        @include(strtolower($item).'::sections.left_sidebar')
                    @endif
                @endif
            @endforeach

            <li><a href="{{ route('admin.faqs.index') }}" class="waves-effect"><i class="icon-docs"></i> <span class="hide-menu"> @lang('app.menu.faq')</span></a></li>
            <li class="pb-30"><a href="{{ route('admin.settings.index') }}" class="waves-effect"><i class="ti-settings"></i> <span class="hide-menu"> @lang('app.menu.settings')</span></a>
            </li>

        </ul>

        <div class="menu-footer">
            <div class="menu-user row">
                <div class="col-lg-4 m-b-5">
                    <div class="btn-group dropup user-dropdown">
                        @if(is_null($user->image))
                            <img  aria-expanded="false" data-toggle="dropdown" src="{{ asset('img/default-profile-3.png') }}" alt="user-img" class="img-circle dropdown-toggle h-30 w-30">

                        @else
                            <img aria-expanded="false" data-toggle="dropdown" src="{{ asset_url('avatar/'.$user->image) }}" alt="user-img" class="img-circle dropdown-toggle h-30 w-30">

                        @endif
                        <ul role="menu" class="dropdown-menu">
                            <li><a class="bg-inverse"><strong class="text-info">{{ ucwords($user->name) }}</strong></a></li>
                            <li>
                                <a href="{{ route('member.dashboard') }}">
                                    <i class="fa fa-sign-in"></i> @lang('app.loginAsEmployee')
                                </a>
                            </li>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();"
                                ><i class="fa fa-power-off"></i> @lang('app.logout')</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>

                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 text-center  m-b-5">
                    <div class="btn-group dropup shortcut-dropdown">
                        <a class="dropdown-toggle waves-effect waves-light text-uppercase" data-toggle="dropdown" href="#">
                            <i class="fa fa-plus"></i>
                        </a>
                        <ul class="dropdown-menu mailbox">

                            @if(in_array('projects',$modules))
                            <li class="top-notifications">
                                <div class="message-center">
                                    <a href="{{ route('admin.projects.create') }}">
                                        <div class="mail-contnet">
                                            <span class="mail-desc m-0">@lang('app.add') @lang('app.project')</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            @endif

                            @if(in_array('tasks',$modules))
                            <li class="top-notifications">
                                <div class="message-center">
                                    <a href="{{ route('admin.all-tasks.create') }}">
                                        <div class="mail-contnet">
                                            <span class="mail-desc m-0">@lang('app.add') @lang('app.task')</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            @endif

                            @if(in_array('clients',$modules))
                            <li class="top-notifications">
                                <div class="message-center">
                                    <a href="{{ route('admin.clients.create') }}">
                                        <div class="mail-contnet">
                                            <span class="mail-desc m-0">@lang('app.add') @lang('app.client')</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            @endif

                            @if(in_array('employees',$modules))
                            <li class="top-notifications">
                                <div class="message-center">
                                    <a href="{{ route('admin.employees.create') }}">
                                        <div class="mail-contnet">
                                            <span class="mail-desc m-0">@lang('app.add') @lang('app.employee')</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            @endif

                            @if(in_array('payments',$modules))
                            <li class="top-notifications">
                                <div class="message-center">
                                    <a href="{{ route('admin.payments.create') }}">
                                        <div class="mail-contnet">
                                            <span class="mail-desc m-0">@lang('modules.payments.addPayment')</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            @endif

                            @if(in_array('tickets',$modules))
                            <li class="top-notifications">
                                <div class="message-center">
                                    <a href="{{ route('admin.tickets.create') }}">
                                        <div class="mail-contnet">
                                            <span class="mail-desc m-0">@lang('app.add') @lang('modules.tickets.ticket')</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            @endif

                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 text-center m-b-5">
                    <div class="btn-group dropup notification-dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell"></i>
                            @if(count($user->unreadNotifications) > 0)

                                <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
                            @endif
                        </a>
                        <div class="dropdown-menu mailbox">

                                <li>
                                    <div class="drop-title">@lang('app.newNotifications') <span class="badge badge-success top-notification-count">{{ count($user->unreadNotifications) }}</span>
                                    </div>
                                </li>
                            <div class="notificationSlimScroll">
                                @foreach ($user->unreadNotifications as $notification)
                                    @if(view()->exists('notifications.member.'.\Illuminate\Support\Str::snake(class_basename($notification->type))))
                                        @include('notifications.member.'.\Illuminate\Support\Str::snake(class_basename($notification->type)))
                                    @endif
                                @endforeach


                            </div>
                            @if(count($user->unreadNotifications) > 0)
                                <li>
                                    <a class="text-center mark-notification-read"
                                       href="javascript:;"> @lang('app.markRead') <i class="fa fa-check"></i> </a>
                                </li>
                            @endif

                        </div>
                    </div>
                </div>

            </div>

            <div class="menu-copy-right">
                <a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="ti-angle-double-right ti-angle-double-left"></i> <span class="collapse-sidebar-text">@lang('app.collapseSidebar')</span></a>
            </div>

        </div>

    </div>


</div>

