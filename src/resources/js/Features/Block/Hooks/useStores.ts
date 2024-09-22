import { usePage } from '@inertiajs/react';
import { Store } from '@/Features/Store';

type PageProps = {
  master: { stores: { bookstores: Store[]; ebookstores: Store[] } };
};

export function useStores() {
  return usePage<PageProps>().props.master.stores;
}
