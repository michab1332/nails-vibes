import { PriceItem } from "@/types";

export const usePriceItemsActions = () => {
    const editRow = (item: PriceItem) => {
        console.log('edit', item);
    }

    const deleteRow = (item: PriceItem) => {
        console.log('delete', item);
    }

    return {
        editRow,
        deleteRow
    };
}
