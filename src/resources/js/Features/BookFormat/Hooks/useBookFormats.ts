import { usePage } from '@inertiajs/react';
import { BookFormat } from '../Types';

type PageProps = {
  master: { bookFormats: BookFormat[] };
};

export function useBookFormats() {
  return usePage<PageProps>().props.master.bookFormats;
}
