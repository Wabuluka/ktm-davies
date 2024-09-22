import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Book } from '../Types';

const fetchBook = (id: number) => {
  return axios
    .get(route('api.books.show', id))
    .then((response) => response.data);
};

type Args = [id: number, options?: Parameters<typeof useQuery<Book, Error>>[2]];

export const useShowBookQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().book.show(id);
  const query = useQuery<Book, Error>(queryKey, () => fetchBook(id), options);

  return { ...query, queryKey };
};
