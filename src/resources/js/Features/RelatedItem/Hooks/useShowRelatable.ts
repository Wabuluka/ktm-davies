import { useShowBookQuery } from '@/Features/Book/Hooks/useShowBookQuery';
import { useShowExternalLinkQuery } from '@/Features/ExternalLink/Hooks/useShowExternalLinkQuery';
import { RelatebleType } from '../Types';

type Props = {
  type: RelatebleType;
  id: number;
};

export function useShowRelatable({ type, id }: Props) {
  const bookQuery = useShowBookQuery(id, { enabled: type === 'book' });
  const linkQuery = useShowExternalLinkQuery(id, {
    enabled: type === 'externalLink',
  });

  return type === 'book' ? { ...bookQuery, type } : { ...linkQuery, type };
}
