(() => {
    const updateFavoriteButton = (form, isFavorite) => {
        const button = form.querySelector('button[type="submit"]');
        if (!button) return;

        const icon = button.querySelector('i, svg');
        const previousBackground = getComputedStyle(button).backgroundColor;
        const previousIconColor = icon ? getComputedStyle(icon).color : '';

        button.setAttribute('aria-label', isFavorite ? 'Remover dos favoritos' : 'Adicionar aos favoritos');
        button.title = isFavorite ? 'Remover favorito' : 'Favoritar';
        button.classList.toggle('bg-red-600', isFavorite);
        button.classList.toggle('text-white', isFavorite);
        button.classList.toggle('bg-white', !isFavorite);
        button.classList.toggle('text-amber-500', !isFavorite);
        button.classList.toggle('border', !isFavorite);
        button.classList.toggle('border-slate-200', !isFavorite);

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const nextBackground = getComputedStyle(button).backgroundColor;
        const nextIconColor = icon ? getComputedStyle(icon).color : '';
        button.animate(
            [{ backgroundColor: previousBackground }, { backgroundColor: nextBackground }],
            { duration: 240, easing: 'ease-out' },
        );

        icon?.animate([
            { transform: 'scale(1) rotate(0deg)', color: previousIconColor },
            { transform: 'scale(.68) rotate(-10deg)', offset: 0.4 },
            { transform: 'scale(1.18) rotate(8deg)', color: nextIconColor, offset: 0.75 },
            { transform: 'scale(1) rotate(0deg)', color: nextIconColor },
        ], { duration: 320, easing: 'ease-out' });
    };

    window.submitFavoriteToggle = async (form) => {
        const methodInput = form.querySelector('input[name="_method"]');
        const removingFavorite = methodInput?.value.toUpperCase() === 'DELETE';
        const csrfToken = form.querySelector('input[name="_token"]')?.value;
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: new FormData(form),
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Não foi possível atualizar os favoritos.');
        }

        const isFavorite = !removingFavorite;
        if (removingFavorite) {
            methodInput.remove();
            form.action = form.dataset.favoriteStoreUrl;
        } else {
            const deleteMethod = document.createElement('input');
            deleteMethod.type = 'hidden';
            deleteMethod.name = '_method';
            deleteMethod.value = 'DELETE';
            form.appendChild(deleteMethod);
            form.action = form.dataset.favoriteDestroyUrl;
        }

        updateFavoriteButton(form, isFavorite);

        if (removingFavorite && form.dataset.removeFavoriteCard === 'true') {
            const card = form.closest('article');
            const list = card?.parentElement;
            card?.remove();
            if (list && !list.querySelector('[data-favorite-product-card]')) {
                document.querySelector('[data-favorites-empty]')?.classList.remove('hidden');
            }
        }

        return { success: true, isFavorite };
    };

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-favorite-toggle]');
        if (!form || form.hasAttribute('data-confirm-favorite-remove')) return;

        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        if (button) button.disabled = true;

        try {
            await window.submitFavoriteToggle(form);
        } catch (error) {
            window.alert(error.message || 'Não foi possível atualizar os favoritos.');
        } finally {
            if (button) button.disabled = false;
        }
    });
})();