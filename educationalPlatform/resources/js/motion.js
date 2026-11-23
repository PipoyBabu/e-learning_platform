import gsap from 'gsap';

const motion = gsap.matchMedia();

motion.add('(prefers-reduced-motion: no-preference)', () => {
    gsap.from('[data-animate="stagger"] > *', {
        y: 12,
        opacity: 0,
        duration: 0.4,
        stagger: 0.05,
        ease: 'power2.out',
    });

    document.querySelectorAll('[data-count-to]').forEach((element) => {
        const target = Number(element.dataset.countTo);
        const state = { value: 0 };

        gsap.to(state, {
            value: target,
            duration: 0.9,
            ease: 'power2.out',
            onUpdate: () => {
                element.textContent = Math.round(state.value).toLocaleString();
            },
        });
    });

    gsap.from('[data-animate="flash"]', {
        x: 12,
        opacity: 0,
        duration: 0.3,
        ease: 'power2.out',
    });

    gsap.from('[data-animate="chart"]', {
        y: 12,
        opacity: 0,
        duration: 0.5,
        stagger: 0.08,
        ease: 'power2.out',
    });
});
