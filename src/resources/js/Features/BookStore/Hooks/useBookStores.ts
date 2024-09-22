import { BookStore } from '@/Features/BookStore/Types';
import { usePage } from '@inertiajs/react';

type PageProps = {
  master: { bookStores: BookStore[] };
};

export function useBookStores() {
  return usePage<PageProps>().props.master.bookStores;
}
