<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Name <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    {!! Form::text('name', null, ['class'=>'form-control text-uppercase', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter Name.', 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>'Name can not have more than {0} letters.', 'maxlength' => '255']) !!}
                    <span class="help-block">Name of the email to uniquely identify this email. Eg. INVALID_USERNAME, UNAUTHORISED_IP etc. </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Description <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    {!! Form::text('description', null, ['class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter Description.', 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>'Description can not have more than {0} letters.', 'maxlength' => '255']) !!}
                    <span class="help-block">Brief description of the email.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Email To</label>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        {!! Form::text('email_to', null, ['class'=>'form-control']) !!}
                    </div>
                    <span class="help-block">You can enter comma(,) separated email addresses.</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Email From</label>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        {!! Form::text('email_from', null, ['class'=>'form-control', 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>'Email From can not have more than {0} letters.']) !!}
                    </div>
                    <span class="help-block">Specify from email address. Eg. iProgrammer Solutions &lt;contact@iprogrammer.com&gt; or just contact@iprogrammer.com</span>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Email CC</label>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        {!! Form::text('email_cc', null, ['class'=>'form-control']) !!}
                    </div>
                    <span class="help-block">You can enter comma(,) separated email addresses.</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Email BCC</label>
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        {!! Form::text('email_bcc', null, ['class'=>'form-control']) !!}
                    </div>
                    <span class="help-block">You can enter comma(,) separated email addresses.</span>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Subject <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    {!! Form::text('subject', null, ['class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter Subject.', 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>'Subject can not have more than {0} letters.']) !!}
                    <span class="help-block">Specify subject of the email.</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Category <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    {!! Form::select('email_type', [''=>'Select Category'] + $emailTo, null,['class'=>'select2me form-control', 'id' => 'email_type', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Category.']) !!}
                    <span class="help-block">Select user group to whom email will be sent.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="note note-info">
        {!! trans('admin::controller/system-email.variable-help') !!}
    </div>
    <div class="row form-group">
        <label class="control-label col-md-2">Email Body</label>

        <div class="col-md-10">
            {!! Form::textarea('text1', null, ['class'=>'editme form-control']) !!}
        </div>
    </div>
    <div class="row form-group">
        <label class="control-label col-md-2">Email Signature</label>
        <div class="col-md-10">
            {!! Form::textarea('text2', null, ['class'=>'editme form-control']) !!}
        </div>
    </div>
</div>