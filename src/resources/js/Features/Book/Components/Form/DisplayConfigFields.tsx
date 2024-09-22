import { BenefitDrawer } from '@/Features/Benefit/Components/BenefitDrawer';
import { AddCustomBlockButton } from '@/Features/Block/Components/AddCustomBlockButton';
import { CharacterDrawer } from '@/Features/Character/Components/CharacterDrawer';
import { StoryDrawer } from '@/Features/Story/Components/StoryDrawer';
import { BookStoreDrawer } from '@/Features/BookBookStore/Components/BookStoreDrawer';
import { EbookStoreDrawer } from '@/Features/BookEbookStore';
import { Box, useDisclosure } from '@chakra-ui/react';
import { RelatedItemDrawer } from '../../../RelatedItem/Components/RelatedItemDrawer';
import { useBookFormState } from '../../Context/BookFormContext';
import { BlockListDND } from '@/Features/Block/Components/BlockListDND';

export const DisplayConfigFields = () => {
  const {
    data: { bookstores, ebookstores, related_items, blocks },
  } = useBookFormState();
  const {
    isOpen: relatedItemIsOpen,
    onClose: relatedItemOnClose,
    onOpen: relatedItemOnOpen,
  } = useDisclosure();
  const {
    isOpen: bookStoreIsOpen,
    onClose: bookStoreOnClose,
    onOpen: bookStoreOnOpen,
  } = useDisclosure();
  const {
    isOpen: ebookStoreIsOpen,
    onClose: ebookStoreOnClose,
    onOpen: ebookStoreOnOpen,
  } = useDisclosure();
  const {
    isOpen: benefitIsOpen,
    onClose: benefitOnClose,
    onOpen: benefitOnOpen,
  } = useDisclosure();
  const {
    isOpen: storyIsOpen,
    onClose: storyOnClose,
    onOpen: storyOnOpen,
  } = useDisclosure();
  const {
    isOpen: characterIsOpen,
    onClose: characterOnClose,
    onOpen: characterOnOpen,
  } = useDisclosure();

  return (
    <Box>
      <Box maxW={{ base: '100%', xl: '100%' }}>
        <BlockListDND
          blocks={blocks.upsert}
          onRelatedBlockEdit={relatedItemOnOpen}
          onBookStoreBlockEdit={bookStoreOnOpen}
          onEbookStoreBlockEdit={ebookStoreOnOpen}
          onBenefitBlockEdit={benefitOnOpen}
          onStoryBlockEdit={storyOnOpen}
          onCharacterBlockEdit={characterOnOpen}
        />
      </Box>
      <AddCustomBlockButton mt={4} />
      <BookStoreDrawer
        bookStores={bookstores}
        isOpen={bookStoreIsOpen}
        onClose={bookStoreOnClose}
      />
      <EbookStoreDrawer
        ebookstores={ebookstores}
        isOpen={ebookStoreIsOpen}
        onClose={ebookStoreOnClose}
      />
      <BenefitDrawer isOpen={benefitIsOpen} onClose={benefitOnClose} />
      <StoryDrawer isOpen={storyIsOpen} onClose={storyOnClose} />
      <RelatedItemDrawer
        relatedItems={related_items}
        isOpen={relatedItemIsOpen}
        onClose={relatedItemOnClose}
      />
      <CharacterDrawer isOpen={characterIsOpen} onClose={characterOnClose} />
    </Box>
  );
};
