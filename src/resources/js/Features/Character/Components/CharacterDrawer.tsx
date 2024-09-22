import { Character } from '@/Features/Character';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { DeleteIcon } from '@chakra-ui/icons';
import {
  Box,
  Button,
  ButtonGroup,
  FormControl,
  FormLabel,
  HStack,
  IconButton,
  Table,
  Tbody,
  Td,
  Text,
  Tr,
  VStack,
} from '@chakra-ui/react';
import { useState } from 'react';
import { EditCharacterDrawer } from './EditCharacterDrawer';
import { SelectCharacterDrawer } from './SelectCharacterDrawer';
import {
  useBookFormState,
  useSetBookFormData,
} from '@/Features/Book/Context/BookFormContext';
import { CharacterSelection } from '@/Features/Character/Components/CharacterSelection';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export function CharacterDrawer({ isOpen, onClose }: Props) {
  const { data } = useBookFormState();
  const initialCharacters = data?.characters || [];
  const [characters, setCharacters] = useState<Character[]>(initialCharacters);
  const { setData } = useSetBookFormData();

  const handleClose = () => {
    const charactersData = data?.characters || [];
    setCharacters(charactersData);
    onClose();
  };

  const handleSelect = (character: Character) => {
    setCharacters((prev) =>
      prev?.find((item) => item.id === character.id)
        ? prev
        : [...(prev ?? []), character],
    );
  };

  const handleUpdate = (character: Character) => {
    setCharacters(
      (prev) =>
        prev?.map((item) =>
          item.id === character.id ? { ...item, ...character } : item,
        ) ?? null,
    );
  };

  const handleDelete = (characterId: number): void => {
    setCharacters((prev) => prev.filter((item) => item.id !== characterId));
  };
  const handleDeleteByMutation = (characterId: number): void => {
    handleDelete(characterId);
    setData((prev) => ({
      ...prev,
      characters: prev.characters?.filter((item) => item.id !== characterId),
    }));
  };

  function handleSubmit() {
    setData('characters', characters);
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={handleClose}>
      <Text>Add a Character</Text>
      <VStack align="stretch">
        <Box>
          <FormControl isRequired>
            <FormLabel>Character</FormLabel>
          </FormControl>
          <HStack spacing={8}>
            <Table maxW="60rem" mb={2}>
              <Tbody>
                {characters.map((character, i) => (
                  <Tr key={i}>
                    <Td>
                      <CharacterSelection
                        characterId={character.id}
                        handleUpdate={handleUpdate}
                      />
                    </Td>
                    <Td>
                      <EditCharacterDrawer
                        character={character}
                        onCharacterDeleted={handleDeleteByMutation}
                        renderOpenDrawerElement={(onOpen) => (
                          <EditButton
                            onClick={onOpen}
                            aria-label="Edit a character"
                          />
                        )}
                      />
                    </Td>
                    <Td
                      w={18}
                      sx={{
                        '.chakra-button': {
                          bg: 'red.500',
                          color: 'white',
                          p: 2,
                        },
                      }}
                    >
                      <IconButton
                        as={DeleteIcon}
                        aria-label="Delete a character"
                        onClick={() => handleDelete(character.id)}
                      />
                    </Td>
                  </Tr>
                ))}
              </Tbody>
            </Table>
          </HStack>
          <SelectCharacterDrawer
            onSubmit={(character: Character) => {
              handleSelect(character);
            }}
            onCharacterDeleted={handleDeleteByMutation}
            renderOpenDrawerElement={(onOpen) => (
              <PrimaryButton onClick={onOpen} aria-label="キャラクターを選択">
                Add
              </PrimaryButton>
            )}
          />
        </Box>
      </VStack>
      <ButtonGroup>
        <Button variant="outline" onClick={handleClose}>
          Back
        </Button>
        <PrimaryButton onClick={handleSubmit}>Save</PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
