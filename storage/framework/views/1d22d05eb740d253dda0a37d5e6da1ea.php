<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    
    <?php if(session('status')): ?>
        <div class="auth-status"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <h2 style="font-family: var(--font-primary); font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; color: var(--text);">
        ENTRAR
    </h2>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>

        
        <div class="auth-field">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username" />
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-400"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="auth-field">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" />
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-400"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="auth-field">
            <label class="remember-label">
                <input type="checkbox" name="remember" />
                Lembrar de mim
            </label>
        </div>

        <div class="auth-actions">
            <?php if(Route::has('password.request')): ?>
                <a class="auth-link" href="<?php echo e(route('password.request')); ?>">Esqueceu a senha?</a>
            <?php endif; ?>
            <button type="submit" class="auth-btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> ENTRAR
            </button>
        </div>

        <?php if(Route::has('register')): ?>
            <p style="margin-top: 20px; text-align: center; font-size: 13px; color: var(--muted);">
                Não tem conta?
                <a href="<?php echo e(route('register')); ?>" class="auth-link" style="color: var(--red); font-weight: 700;">Registre-se</a>
            </p>
        <?php endif; ?>
    </form>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\fitpulse-api\resources\views/auth/login.blade.php ENDPATH**/ ?>