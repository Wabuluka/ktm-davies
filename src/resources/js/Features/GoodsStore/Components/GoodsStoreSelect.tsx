import { useGoodsStores } from '@/Features/GoodsStore/Hooks/useGoodsStores';
import { Select, SelectProps } from '@chakra-ui/react';

type Props = SelectProps & {
  disabledStoreIds?: number[];
};

export function GoodsStoreSelect({ disabledStoreIds, ...props }: Props) {
  const goodsStores = useGoodsStores();

  return (
    <Select {...props}>
      <option value="">Please select</option>
      {goodsStores.map((goodsStore) => (
        <option
          key={goodsStore.id}
          value={goodsStore.id}
          disabled={disabledStoreIds?.includes(goodsStore.id)}
        >
          {goodsStore.store.name}
        </option>
      ))}
    </Select>
  );
}
