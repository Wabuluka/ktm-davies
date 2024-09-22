import { useMemo } from 'react';
import { useGoodsStores } from './useGoodsStores';

export function useGoodsStore(goodsStoreId: string | number) {
  const goodsStores = useGoodsStores();

  return useMemo(
    () => goodsStores.find((store) => store.id == goodsStoreId),
    [goodsStores, goodsStoreId],
  );
}
