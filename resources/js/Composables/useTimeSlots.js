/**
 * useTimeSlots — Composable bersama untuk generate slot waktu dan validasi
 * Digunakan oleh: CourtDetail.vue, BookingSchedule.vue
 */
import { computed } from 'vue'

/**
 * Generate daftar time slots berdasarkan jam buka/tutup lapangan.
 * Tandai slot yang sudah dipesan sebagai `booked`.
 *
 * @param {Ref<string>} selectedDate       - tanggal yang dipilih (YYYY-MM-DD)
 * @param {Ref<string|null>} selectedCourtDetail - ID sub-lapangan yang dipilih (nullable)
 * @param {Object} court                   - data lapangan (open_time, close_time)
 * @param {Array} bookedSlots              - array slot yang sudah di-booking dari server
 * @returns {{ timeSlots: ComputedRef }}
 */
export function useTimeSlots(selectedDate, selectedCourtDetail, court, bookedSlots) {
    const timeSlots = computed(() => {
        const slots = []
        const open  = 6
        const close = parseInt(court.close_time?.split(':')[0] ?? 23)

        const d = new Date(selectedDate.value)
        const isWeekend = d.getDay() === 0 || d.getDay() === 6

        for (let h = open; h <= close; h++) {
            const time = `${String(h).padStart(2, '0')}:00`
            const end  = `${String(h + 1).padStart(2, '0')}:00`

            const booked = (bookedSlots ?? []).some(b => {
                const dateMatch  = String(b.date).substring(0, 10) === String(selectedDate.value).substring(0, 10)
                const timeMatch  = b.items && Array.isArray(b.items) && b.items.includes(time)
                const courtMatch = !selectedCourtDetail?.value
                    || !b.court_detail
                    || b.court_detail === selectedCourtDetail.value

                return dateMatch && timeMatch && courtMatch
            })

            let isPeak = false
            let isWeekendSlot = isWeekend

            const hInt = parseInt(time.split(':')[0])
            if (!isWeekendSlot && hInt >= 17 && hInt <= 23) {
                isPeak = true
            }

            let multiplier = 1.0
            if (isWeekendSlot) {
                multiplier = 1.5 // Weekend flat increase
            } else if (isPeak) {
                multiplier = 1.2 // Weekday peak hours increase
            }

            const price = (court.price_per_hour ?? 0) * multiplier

            slots.push({ time, end, booked, price, isPeak })
        }
        return slots
    })

    return { timeSlots }
}

/**
 * Validasi apakah array slot yang dipilih berurutan (konsekutif).
 * Contoh valid: ['08:00', '09:00', '10:00']
 * Contoh invalid: ['08:00', '10:00'] — ada gap di 09:00
 *
 * @param {string[]} slots - array jam yang dipilih, e.g. ['08:00', '09:00']
 * @returns {boolean}
 */
export function areConsecutive(slots) {
    if (slots.length <= 1) return true
    const sorted = [...slots].sort()
    for (let i = 1; i < sorted.length; i++) {
        const prevHour = parseInt(sorted[i - 1].split(':')[0])
        const currHour = parseInt(sorted[i].split(':')[0])
        if (currHour !== prevHour + 1) return false
    }
    return true
}
