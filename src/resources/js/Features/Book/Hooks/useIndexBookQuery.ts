import { Paginator } from '@/Api/Types';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useState } from 'react';
import { useQuery } from 'react-query';
import { Book, BookStatus } from '../Types';

export type IndexBookQueryParams = {
  keyword?: string;
  statuses: BookStatus[];
  sites: string[];
  page: number;
};

const fetchBooks = (params?: IndexBookQueryParams) => {
  return axios
    .get(route('api.books.index'), {
      params,
    })
    .then((response) => response.data);
};

export function useIndexBookQuery(
  params?: IndexBookQueryParams,
  options: Parameters<typeof useQuery<Paginator<Book>, Error>>[2] = {
    cacheTime: 0,
  },
) {
  const [queryParams, setQueryParams] = useState<IndexBookQueryParams>(
    params || {
      keyword: '',
      statuses: [],
      sites: [],
      page: 1,
    },
  );
  const queryKeys = useQueryKeys();
  const queryKey = queryKeys.book.index(queryParams);
  const query = useQuery<Paginator<Book>, Error>(
    queryKey,
    () => fetchBooks(queryParams),
    options,
  );

  return { ...query, setQueryParams, queryKey };
}
