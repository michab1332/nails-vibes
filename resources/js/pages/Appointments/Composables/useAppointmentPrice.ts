import type { Ref } from 'vue';
import { ref, watch } from 'vue';
import type { PriceItem } from '@/types/price-item';

export function useAppointmentPrice(
    selectedPriceItems: Ref<number[]>,
    priceItems: PriceItem[],
    currentPrice: Ref<number | string>
) {
    const isDirtyPrice = ref(false);

    // Initial check - if price is not 0, assume it was already set (e.g. on edit)
    if (Number(currentPrice.value) > 0) {
        isDirtyPrice.value = true;
    }

    watch(selectedPriceItems, (newIds) => {
        // Only auto-calculate if user hasn't manually changed the price
        // or if they cleared the price field
        if (!isDirtyPrice.value || currentPrice.value === '' || currentPrice.value === 0 || currentPrice.value === '0') {
            const totalMinPrice = newIds.reduce((sum, id) => {
                const item = priceItems.find((p) => p.id === id);

                return sum + (item ? Number(item.price_min) : 0);
            }, 0);

            currentPrice.value = totalMinPrice;
            // If we just set it from 0, keep it clean unless it's a manual change
        }
    }, { deep: true });

    const handleManualPriceChange = () => {
        isDirtyPrice.value = true;
    };

    const resetDirtyFlag = () => {
        isDirtyPrice.value = false;
    };

    return {
        isDirtyPrice,
        handleManualPriceChange,
        resetDirtyFlag
    };
}
