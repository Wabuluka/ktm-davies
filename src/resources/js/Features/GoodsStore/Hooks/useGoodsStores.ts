import { GoodsStore } from '@/Features/GoodsStore/Types';
import { usePage } from '@inertiajs/react';

type PageProps = {
  master: { goodsStores: GoodsStore[] };
};

export function useGoodsStores() {
  return usePage<PageProps>().props.master.goodsStores;
}
