import { Character } from '@/Features/Character';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { SearchForm } from '@/UI/Components/Form/Input/SearchForm';
import { DrawerPagenator } from '@/UI/Components/Navigation/DrawerPaginator';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { createPaginationLinks } from '@/UI/Utils/createPaginationLinks';
import {
  Box,
  Button,
  ButtonGroup,
  Center,
  FormControl,
  FormLabel,
  Input,
  Radio,
  RadioGroup,
  Table,
  Tbody,
  Td,
  Text,
  Tr,
  VStack,
  useDisclosure,
} from '@chakra-ui/react';
import { FC, useEffect, useState } from 'react';
import {
  QueryParams,
  useIndexCharactersQuery,
} from '../Hooks/useIndexCharactersQuery';
import { CreateCharacterDrawer } from './CreateCharacterDrawer';
import { EditCharacterDrawer } from './EditCharacterDrawer';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import { SelectSeriesDrawer } from '@/Features/Series';
import { SeriesSelection } from '@/Features/Series/Components/SeriesSelection';

type Props = {
  onSubmit: (character: Character) => void;
  onCharacterDeleted: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const SelectCharacterDrawer: FC<Props> = ({
  onSubmit,
  onCharacterDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [character, setCharacter] = useState('');
  const [seriesId, setSeriesId] = useState<number | undefined>();
  const [queryParams, setQueryParams] = useState<QueryParams>();
  const [selectedCharactersInDrawer, setSelectedCharactersInDrawer] =
    useState<Character | null>(null);
  const [currentIndex, setCurrentIndex] = useState(0);
  const { characters, lastPage, isLoading, queryKey } =
    useIndexCharactersQuery(queryParams);
  const { pagenationLinks } = createPaginationLinks(location.href, lastPage);

  const handleClose = () => {
    setCharacter('');
    setSeriesId(undefined);
    setQueryParams(undefined);
    setCurrentIndex(0);
    onClose();
  };

  const handleSelectionSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    e.stopPropagation();
    if (!selectedCharactersInDrawer) return;
    onSubmit(selectedCharactersInDrawer);
    handleClose();
  };

  const handleSearchSubmit = () => {
    setCurrentIndex(0);
    setQueryParams({ name: character, seriesId: seriesId });
  };

  const handlePaginator = (index: number) => {
    setCurrentIndex(index);
  };

  useEffect(() => {
    setQueryParams((prevParams) => ({
      ...prevParams,
      currentIndex: currentIndex + 1,
    }));
  }, [currentIndex]);

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Select a character</Text>

        <VStack align="stretch">
          <SearchForm onSubmit={handleSearchSubmit}>
            <FormControl isRequired>
              <FormLabel>Character Name</FormLabel>
              <Input
                type="text"
                name="name"
                value={character}
                onChange={(e) => setCharacter(e.target.value)}
              />
            </FormControl>
            <FormControl>
              <FormLabel>Related Series</FormLabel>
              {seriesId && (
                <SeriesSelection
                  seriesId={seriesId}
                  onUnselect={() => setSeriesId(undefined)}
                />
              )}
              <SelectSeriesDrawer
                onSubmit={(seriesId) => setSeriesId(seriesId)}
                selectedSeriesId={seriesId}
                renderOpenDrawerElement={(onOpen) => (
                  <PrimaryButton onClick={onOpen}>Select</PrimaryButton>
                )}
              />
            </FormControl>
          </SearchForm>

          {isLoading ? (
            <LoadingSpinner />
          ) : characters ? (
            <Box>
              <form id="character-selection" onSubmit={handleSelectionSubmit}>
                <RadioGroup value={selectedCharactersInDrawer?.id.toString()}>
                  <Table maxW="60rem" mb={2}>
                    <Tbody>
                      {characters.map((character) => (
                        <Tr key={character.id}>
                          <Td>
                            <Radio
                              name="character"
                              value={character.id.toString()}
                              onChange={() =>
                                setSelectedCharactersInDrawer(character)
                              }
                              checked={
                                character.id === selectedCharactersInDrawer?.id
                              }
                            />
                          </Td>
                          <Td p={0}>
                            {!!character.thumbnail && (
                              <Center p={1}>
                                <PreviewableThumbnail
                                  previewTriggerProps={{
                                    'aria-label': 'Preview thumbnail',
                                  }}
                                  imageProps={{
                                    src: character.thumbnail.original_url,
                                    alt: '',
                                  }}
                                />
                              </Center>
                            )}
                          </Td>
                          <Td>{character.name}</Td>
                          <Td>
                            <EditCharacterDrawer
                              character={character}
                              onCharacterDeleted={onCharacterDeleted}
                              renderOpenDrawerElement={(onOpen) => (
                                <EditButton
                                  onClick={onOpen}
                                  aria-label="Edit character"
                                />
                              )}
                            />
                          </Td>
                        </Tr>
                      ))}
                    </Tbody>
                  </Table>

                  <DrawerPagenator
                    pageChange={handlePaginator}
                    links={pagenationLinks}
                    currentIndex={currentIndex}
                  />
                </RadioGroup>
              </form>
            </Box>
          ) : (
            <DataFetchError />
          )}

          <Box>
            <CreateCharacterDrawer
              queryKey={queryKey}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Create</PrimaryButton>
              )}
            />
          </Box>
        </VStack>

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            form="character-selection"
            type="submit"
            isDisabled={!selectedCharactersInDrawer}
          >
            Select
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
