<script lang="ts">
    import { onMount, createEventDispatcher } from 'svelte';

    const dispatch = createEventDispatcher();

    let bootLogs: string[] = [];
    let preBootLogs: string[] = [];
    let showCursor = true;
    let showBios = false;
    let showLogs = false;
    let showLoadingUI = false;

    const biosText = [
        'Phoenix Technologies LTD VGA BIOS. Copyright (C) 1990-2026',
        'Ganesha CTF Platform Initializing...',
        '',
        'CPU: Intel(R) Core(TM) i9-14900K CPU @ 3.20GHz',
        'Memory: 65536M OK',
        '',
        'Detecting Primary Master ... [Ganesha SSD 2TB]',
        'Detecting Primary Slave  ... [None]',
        'Detecting Secondary Master.. [None]',
        'Detecting Secondary Slave .. [None]',
        '',
        'Press DEL to enter SETUP',
        'Booting from Primary Master...',
    ];

    const linuxLogs = [
        '[    0.000000] Linux version 6.5.0-generic (root@ganesha) (gcc (Ubuntu 11.4.0) 11.4.0) #1 SMP PREEMPT_DYNAMIC',
        '[    0.000000] Command line: BOOT_IMAGE=/boot/vmlinuz-6.5.0 root=UUID=8c8... ro quiet splash',
        '[    0.021034] kernel system: Booting paravirtualized kernel on bare hardware',
        '[    0.054321] smpboot: CPU0: Intel(R) Core(TM) i9-14900K CPU @ 3.20GHz',
        '[    0.101234] Initializing cgroup subsys cpuset',
        '[    0.123456] Initializing cgroup subsys cpu',
        '[    0.145678] Initializing cgroup subsys cpuacct',
        '[    0.200111] CPU: Physical Processor ID: 0',
        '[    0.345678] ACPI: Core revision 20230628',
        '[    0.412345] Setting APIC routing to flat',
        '[    0.501234] ..TIMER: vector=0x30 apic1=0 pin1=2 apic2=-1 pin2=-1',
        '[    0.654321] clocksource: tsc: mask: 0xffffffffffffffff max_cycles: 0x2e061ecdb32',
        '[    0.801234] Calibrating delay loop.. 6400.00 BogoMIPS (lpj=12800000)',
        '[    1.002345] pid_max: default: 32768 minimum: 301',
        '[    1.123456] Mount-cache hash table entries: 131072',
        '[    1.234567] Mountpoint-cache hash table entries: 32768',
        '[    1.345678] smpboot: x86: Booting SMP configuration:',
        '[    1.456789] .... node  #0, CPUs:      #1 #2 #3 #4 #5 #6 #7',
        '[    1.567890] smp: Brought up 1 node, 8 CPUs',
        '[    1.678901] smpboot: Max logical packages: 1',
        '[    1.789012] smpboot: Total of 8 processors activated (51200.00 BogoMIPS)',
        '[    2.102345] Run /init as init process',
        '[    2.201234] Loading modules...',
        '[  OK  ] Started udev Kernel Device Manager.',
        '[  OK  ] Reached target Local File Systems.',
        '[  OK  ] Started Network Manager.',
        '[  OK  ] Started Ganesha CTF Main Service.',
        '!!FLAG_ANIMATION!!',
        '[FAILED] Failed to start Remote Debugging Backdoor.',
        '[  OK  ] Reached target Multi-User System.',
        '[  OK  ] Started Frontend UI Interface...',
        'Welcome to Ganesha CTF Platform.',
    ];

    function formatLog(log: string) {
        if (log.startsWith('[  OK  ]')) {
            return `<span class="text-white">[  <span class="text-green-500 font-bold">OK</span>  ]</span>${log.substring(8)}`;
        }

        if (log.startsWith('[FAILED]')) {
            return `<span class="text-white">[<span class="text-red-500 font-bold">FAILED</span>]</span>${log.substring(8)}`;
        }
        if (log.match(/^\[\s*\d+\.\d+\]/)) {
            // color the timestamp
            return log.replace(
                /^(\[\s*\d+\.\d+\])/,
                '<span class="text-gray-500">$1</span>',
            );
        }
        return log;
    }

    let containerElement: HTMLElement;

    onMount(() => {
        // 1. Initial blinking cursor
        setTimeout(() => {
            preBootLogs = ['Connecting to WireGuard VPN...'];

            setTimeout(() => {
                preBootLogs = [
                    ...preBootLogs,
                    'Establishing secure handshake...',
                ];

                setTimeout(() => {
                    preBootLogs = [
                        ...preBootLogs,
                        "<span class='text-white'>[  <span class='text-green-500 font-bold'>OK</span>  ]</span> Tunnel established.",
                    ];

                    setTimeout(() => {
                        showCursor = false;
                        showBios = true;

                        // 2. Show BIOS
                        setTimeout(() => {
                            showBios = false;
                            showLogs = true;

                            let delay = 0;
                            linuxLogs.forEach((log, index) => {
                                // SPAMMY logs in the middle, slow at start and end
                                let step = Math.random() * 40;

                                if (index < 4 || index > linuxLogs.length - 8) {
                                    step = 100 + Math.random() * 250;
                                }

                                if (log === '!!FLAG_ANIMATION!!') {
                                    step += 1500; // Pause for the flag animation to finish
                                }

                                delay += step;

                                setTimeout(() => {
                                    if (log === '!!FLAG_ANIMATION!!') {
                                        const id = 'flag-anim-' + Date.now();
                                        bootLogs = [
                                            ...bootLogs,
                                            `<span class="text-yellow-500 font-bold">Retrieving hidden flag CTF{<span id="${id}">...</span>}</span>`,
                                        ];

                                        // Start randomizing the characters
                                        const charset =
                                            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
                                        let interval = setInterval(() => {
                                            const el =
                                                document.getElementById(id);
                                            if (el) {
                                                let randStr = '';
                                                for (let i = 0; i < 24; i++) {
                                                    randStr += charset.charAt(
                                                        Math.floor(
                                                            Math.random() *
                                                                charset.length,
                                                        ),
                                                    );
                                                }
                                                el.innerText = randStr;
                                            } else {
                                                clearInterval(interval);
                                            }
                                        }, 30);

                                        // Stop after 1.4 seconds and resolve to a fake flag
                                        setTimeout(() => {
                                            clearInterval(interval);
                                            const el =
                                                document.getElementById(id);
                                            if (el) {
                                                el.innerText =
                                                    'n1c3_try_bUt_n0t_h3r3!';
                                            }
                                        }, 1400);
                                    } else {
                                        bootLogs = [
                                            ...bootLogs,
                                            formatLog(log),
                                        ];
                                    }

                                    // scroll to bottom
                                    if (containerElement) {
                                        containerElement.scrollTop =
                                            containerElement.scrollHeight;
                                    }
                                }, delay);
                            });

                            // End sequence after all logs + some delay
                            setTimeout(() => {
                                showLogs = false;
                                showLoadingUI = true;

                                setTimeout(() => {
                                    dispatch('bootComplete');
                                }, 1200); // 1.2 seconds for loading UI
                            }, delay + 800); // Wait 0.8 seconds after last log before fading to Loading UI
                        }, 1800); // 1.8 seconds for BIOS
                    }, 800); // Wait 0.8s after tunnel established before showing BIOS
                }, 800); // 0.8s for handshake
            }, 800); // 0.8s for connecting
        }, 800); // 0.8s for initial blinking cursor
    });
</script>

<div
    bind:this={containerElement}
    class="fixed inset-0 z-[100] bg-black text-green-500 font-mono text-sm sm:text-base overflow-y-auto p-4 sm:p-8 flex flex-col items-start select-none"
    id="boot-container"
>
    <!-- CRT Overlay effect -->
    <div
        class="pointer-events-none fixed inset-0 z-[101] bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%]"
    ></div>
    <div
        class="pointer-events-none fixed inset-0 z-[102] shadow-[inset_0_0_100px_rgba(0,0,0,0.9)]"
    ></div>

    {#if showCursor}
        <div class="flex flex-col w-full text-gray-300">
            {#each preBootLogs as log}
                <p class="min-h-[1.5rem]">{@html log}</p>
            {/each}
            <div class="animate-pulse w-3 h-5 bg-gray-300 mt-1"></div>
        </div>
    {/if}

    {#if showBios}
        <div class="flex flex-col w-full text-gray-300">
            {#each biosText as text}
                <p class="min-h-6">{text}</p>
            {/each}
            <div class="mt-2 animate-pulse w-3 h-5 bg-gray-300"></div>
        </div>
    {/if}

    {#if showLogs}
        <div
            class="flex flex-col w-full text-left text-gray-300 relative z-10 pb-8"
        >
            {#each bootLogs as log}
                <p class="mb-0.5 break-all">{@html log}</p>
            {/each}
            {#if bootLogs.length < linuxLogs.length}
                <div class="animate-pulse w-3 h-5 bg-gray-300 mt-1"></div>
            {/if}
        </div>
    {/if}

    {#if showLoadingUI}
        <div
            class="flex items-center justify-center w-full h-full absolute inset-0 bg-black z-[110]"
        >
            <div class="text-white text-base sm:text-lg font-sans">
                Loading UI...
            </div>
        </div>
    {/if}
</div>

<style>
    /* Add a slight CRT flicker effect */
    @keyframes flicker {
        0% {
            opacity: 0.95;
        }
        5% {
            opacity: 0.85;
        }
        10% {
            opacity: 0.95;
        }
        15% {
            opacity: 1;
        }
        100% {
            opacity: 1;
        }
    }
    #boot-container {
        animation: flicker 0.15s infinite;
    }
</style>
