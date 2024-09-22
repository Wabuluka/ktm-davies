import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { ExternalLink } from '../Types';

const fetchExternalLink = (id: number) => {
  return axios
    .get(route('external-links.show', id))
    .then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<ExternalLink, Error>>[2],
];

export const useShowExternalLinkQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().externalLinks.show(id);
  const query = useQuery<ExternalLink, Error>(
    queryKey,
    () => fetchExternalLink(id),
    options,
  );

  return { ...query, queryKey };
};
