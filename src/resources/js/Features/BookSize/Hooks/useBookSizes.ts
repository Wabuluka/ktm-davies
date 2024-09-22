import { usePage } from '@inertiajs/react';
import { BookSize } from '../Types';

type PageProps = {
  master: { bookSizes: BookSize[] };
};

export function useBookSizes() {
  return usePage<PageProps>().props.master.bookSizes;
}
