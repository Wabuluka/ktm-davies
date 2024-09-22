import { usePage } from '@inertiajs/react';
import { EbookStore } from '@/Features/EbookStore/Types';

type PageProps = {
  master: { ebookStores: EbookStore[] };
};

export function useEbookStores() {
  return usePage<PageProps>().props.master.ebookStores;
}
