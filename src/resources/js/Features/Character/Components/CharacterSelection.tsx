import { useShowCharacterQuery } from '@/Features/Character/Hooks/useShowCharacterQuery';
import { Character } from '@/Features/Character';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { Box } from '@chakra-ui/react';
import { useEffect } from 'react';

type Props = {
  characterId: number;
  handleUpdate: (character: Character) => void;
};

export function CharacterSelection({ characterId, handleUpdate }: Props) {
  const { data, isLoading, isError } = useShowCharacterQuery(characterId);

  useEffect(() => {
    if (data) {
      handleUpdate(data);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data]);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Box> {data.name} </Box>;
}
