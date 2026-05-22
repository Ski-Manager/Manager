<!-- Contact page flash messages -->
<?php $flash = $this->session->flashdata('msg'); ?>
<?php if ($flash): ?>
<div class="contact-flash-wrap">
    <?php echo $flash; ?>
</div>
<?php endif; ?>

<!-- Contact hero banner -->
<div class="contact-hero mb-4">
    <div class="contact-hero-inner">
        <div class="contact-hero-icon"><i class="fa-solid fa-envelope"></i></div>
        <h1 class="h1 contact-hero-title"><?php echo $this->lang->line('contact_form')['page_title']; ?></h1>
        <p class="contact-hero-sub"><?php echo $this->lang->line('contact_form')['contact_text']; ?></p>
    </div>
</div>

<!-- Main content: info + form -->
<div class="grid gap-4 mb-4">

    <!-- Left column: info cards -->
    <div class="col-span-12 lg:col-span-5 xl:col-span-4">

        <!-- Site ownership card -->
        <div class="contact-info-card mb-3">
            <div class="contact-info-icon-wrap"><i class="fa-solid fa-circle-info"></i></div>
            <div>
                <h5 class="h5 contact-info-title"><?php echo $this->lang->line('contact_form')['ownership_title']; ?></h5>
                <p class="contact-info-text"><?php echo sprintf($this->lang->line('contact_form')['ownership_text'], base_url('about')); ?></p>
            </div>
        </div>

        <!-- Response time card -->
        <div class="contact-info-card mb-3">
            <div class="contact-info-icon-wrap"><i class="fa-solid fa-clock"></i></div>
            <div>
                <h5 class="h5 contact-info-title"><?php echo $this->lang->line('contact_form')['response_title']; ?></h5>
                <p class="contact-info-text"><?php echo $this->lang->line('contact_form')['response_text']; ?></p>
            </div>
        </div>

        <!-- Common topics card -->
        <div class="contact-info-card mb-3">
            <div class="contact-info-icon-wrap"><i class="fa-solid fa-comment-dots"></i></div>
            <div>
                <h5 class="h5 contact-info-title"><?php echo $this->lang->line('contact_form')['topics_title']; ?></h5>
                <p class="contact-info-text mb-2"><?php echo $this->lang->line('contact_form')['topics_text']; ?></p>
                <ul class="contact-topic-list">
                    <li><i class="fa-solid fa-bug mr-2"></i><?php echo $this->lang->line('contact_form')['topic_1']; ?></li>
                    <li><i class="fa-solid fa-user-lock mr-2"></i><?php echo $this->lang->line('contact_form')['topic_2']; ?></li>
                    <li><i class="fa-solid fa-lightbulb mr-2"></i><?php echo $this->lang->line('contact_form')['topic_3']; ?></li>
                    <li><i class="fa-solid fa-chart-line mr-2"></i><?php echo $this->lang->line('contact_form')['topic_4']; ?></li>
                    <li><i class="fa-solid fa-shield-halved mr-2"></i><?php echo $this->lang->line('contact_form')['topic_5']; ?></li>
                </ul>
            </div>
        </div>

        <!-- Direct email card -->
        <div class="contact-info-card mb-3">
            <div class="contact-info-icon-wrap"><i class="fa-solid fa-envelope"></i></div>
            <div>
                <h5 class="h5 contact-info-title"><?php echo $this->lang->line('contact_form')['email_title']; ?></h5>
                <p class="contact-info-text"><?php echo $this->lang->line('contact_form')['email_text']; ?></p>
            </div>
        </div>

        <!-- Advertising card -->
        <div class="contact-info-card mb-0">
            <div class="contact-info-icon-wrap"><i class="fa-solid fa-bullhorn"></i></div>
            <div>
                <h5 class="h5 contact-info-title"><?php echo $this->lang->line('contact_form')['advertising_title']; ?></h5>
                <p class="contact-info-text"><?php echo $this->lang->line('contact_form')['advertising_text']; ?></p>
            </div>
        </div>

    </div><!-- /left column -->

    <!-- Right column: contact form -->
    <div class="col-span-12 lg:col-span-7 xl:col-span-8">
        <div class="contact-form-card">

            <h4 class="h4 contact-form-title">
                <i class="fa-solid fa-paper-plane mr-2"></i><?php echo $this->lang->line('contact_form')['form_title']; ?>
            </h4>

            <?php
            $attributes = array('class' => '', 'name' => 'contactform');
            echo form_open('contact_controller/check_contact_form', $attributes);
            ?>

            <div class="mb-3">
                <label for="name" class="contact-field-label">
                    <i class="fa-solid fa-user mr-1"></i><?php echo $this->lang->line('contact_form')['name']; ?>
                </label>
                <input id="name" name="name" type="text" class="input w-full contact-input"
                       placeholder="<?php echo $this->lang->line('contact_form')['contact_name_field']; ?>"
                       value="<?php echo set_value('name'); ?>" maxlength="100" />
                <?php if (isset($contact_error_name)) echo '<div class="contact-field-error mt-1">'.$contact_error_name.'</div>'; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="contact-field-label">
                    <i class="fa-solid fa-envelope mr-1"></i><?php echo $this->lang->line('home')['email']; ?>
                </label>
                <input id="email" name="email" type="email" class="input w-full contact-input"
                       placeholder="<?php echo $this->lang->line('contact_form')['email_field']; ?>"
                       value="<?php echo set_value('email'); ?>" maxlength="254" />
                <?php if (isset($contact_error_email)) echo '<div class="contact-field-error mt-1">'.$contact_error_email.'</div>'; ?>
            </div>

            <div class="mb-3">
                <label for="subject" class="contact-field-label">
                    <i class="fa-solid fa-tag mr-1"></i><?php echo $this->lang->line('contact_form')['subject']; ?>
                </label>
                <input id="subject" name="subject" type="text" class="input w-full contact-input"
                       placeholder="<?php echo $this->lang->line('contact_form')['contact_subject_field']; ?>"
                       value="<?php echo set_value('subject'); ?>" maxlength="200" />
                <?php if (isset($contact_error_subject)) echo '<div class="contact-field-error mt-1">'.$contact_error_subject.'</div>'; ?>
            </div>

            <div class="mb-3">
                <label for="message" class="contact-field-label">
                    <i class="fa-solid fa-message mr-1"></i><?php echo $this->lang->line('contact_form')['message']; ?>
                </label>
                <textarea id="message" name="message" rows="7" class="textarea w-full contact-input"
                          placeholder="<?php echo $this->lang->line('contact_form')['contact_message_field']; ?>"
                          maxlength="2000"><?php echo set_value('message'); ?></textarea>
                <div class="contact-char-counter">
                    <span id="msg-char-count">0</span> / 2000
                </div>
                <?php if (isset($contact_error_message)) echo '<div class="contact-field-error mt-1">'.$contact_error_message.'</div>'; ?>
            </div>

            <div class="contact-captcha-block mb-4">
                <div class="contact-captcha-label"><?php echo $captcha['label']; ?></div>
                <div class="contact-captcha-row">
                    <?php echo $captcha['img']; ?>
                    <?php echo $captcha['refresh']; ?>
                </div>
                <div class="mt-2">
                    <?php echo $captcha['input']; ?>
                </div>
            </div>

            <div class="mb-0">
                <button type="submit" name="submit" class="btn btn-primary contact-submit-btn">
                    <i class="fa-solid fa-paper-plane mr-2"></i><?php echo $this->lang->line('contact_form')['send']; ?>
                </button>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div><!-- /right column -->

</div><!-- /row -->

<script>
(function () {
    var ta = document.getElementById('message');
    var counter = document.getElementById('msg-char-count');
    if (ta && counter) {
        counter.textContent = ta.value.length;
        ta.addEventListener('input', function () {
            counter.textContent = ta.value.length;
        });
    }
}());
</script>

