<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-900 text-white">
        <h1 class="text-4xl font-bold mb-6">Werdl</h1>
        <div class="grid gap-2" :style="{ gridTemplateRows: 'repeat(6, 1fr)', gridTemplateColumns: 'repeat(5, 1fr)' }">
            <div v-for="(letter, i) in flatGrid" :key="i"
                 class="w-12 h-12 flex items-center justify-center text-2xl font-bold border border-gray-600 rounded"
                 :class="colorClass(letter.status)">
                {{ letter.letter }}
            </div>
        </div>

        <p v-if="gameStatus === 'won'" class="mt-6 text-green-400 font-semibold">You got it!</p>
        <p v-if="gameStatus === 'lost'" class="mt-6 text-red-400 font-semibold">Out of guesses!</p>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted, onUnmounted } from 'vue'
    import axios from 'axios'

    interface GuessResult {
        letter: string;
        status: 'green' | 'yellow' | 'gray' | null;
    }

    interface LetterResult {
        letter: string;
        status: 'green' | 'yellow' | 'gray' | null;
    }

    const guesses = ref<GuessResult[][]>([])
    const currentGuess = ref('')
    const maxGuesses = 6
    const gameStatus = ref<'won' | 'lost' | null>(null)


    function colorClass(status: string) {
        return {
            green: 'bg-green-600',
            yellow: 'bg-yellow-500',
            gray: 'bg-gray-700',
            typing: 'bg-gray-600 border-gray-400',
            null: 'bg-gray-800',
        }[status || 'null']
    }

    async function submitGuess(word: string) {
        try {
            const { data } = await axios.post('/api/guess', { guess: word })
            guesses.value.push(data.result)

            if (data.result.every((letter: LetterResult) => letter.status === 'green')) {
                gameStatus.value = 'won'
            } else if (guesses.value.length >= maxGuesses) {
                gameStatus.value = 'lost'
            }
        } catch (err) {
            console.error('Invalid guess or API error', err)
        }
    }

    const flatGrid = computed(() => {
        const grid = []

        for (let i = 0; i < maxGuesses; i++) {
            const guess = guesses.value[i]

            if (guess) {
                // Completed guess - show results
                for (let j = 0; j < 5; j++) {
                    grid.push(guess[j] || { letter: '', status: null })
                }
            } else if (i === guesses.value.length && currentGuess.value && !gameStatus.value) {
                // Current row being typed
                for (let j = 0; j < 5; j++) {
                    grid.push({
                        letter: currentGuess.value[j] || '',
                        status: 'typing'
                    })
                }
            } else {
                // Empty row
                for (let j = 0; j < 5; j++) {
                    grid.push({ letter: '', status: null })
                }
            }
        }

        return grid
    })

    function handleKey(event: KeyboardEvent) {
        if (gameStatus.value) return // Game is over

        const key = event.key.toLowerCase()

        if (key === 'enter') {
            if (currentGuess.value.length === 5) {
                submitGuess(currentGuess.value)
                currentGuess.value = ''
            }
        } else if (key === 'backspace') {
            currentGuess.value = currentGuess.value.slice(0, -1)
        } else if (key.match(/^[a-z]$/) && currentGuess.value.length < 5) {
            currentGuess.value += key
        }
    }

    onMounted(() => {
        window.addEventListener('keydown', handleKey)
    })

    onUnmounted(() => {
        window.removeEventListener('keydown', handleKey)
    })
</script>
