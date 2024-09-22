import {
  Box,
  Hide,
  HStack,
  Icon,
  LinkBox,
  List,
  ListItem,
  Select,
} from '@chakra-ui/react';
import {
  BsChevronDoubleLeft,
  BsChevronDoubleRight,
  BsChevronLeft,
  BsChevronRight,
} from 'react-icons/bs';
import { Link } from './Link';

type Props = {
  onPageChange: (index: number) => void;
  lastPage: number;
  currentIndex: number;
};

export const PaginatorBase = ({
  onPageChange,
  lastPage,
  currentIndex,
}: Props) => {
  if (lastPage <= 1) {
    return null;
  }
  const page = lastPage < currentIndex || currentIndex <= 1 ? 1 : currentIndex;
  const shouldShowFirst = currentIndex !== 1;
  const shouldShowPrev = currentIndex > 2;
  const shouldShowNext = currentIndex < lastPage - 1;
  const shouldShowLast = currentIndex !== lastPage;
  function handlePageChange(page: number) {
    onPageChange(page);
  }
  function pageChangeOnAnchorElementHandler(page: number) {
    return (
      e:
        | React.MouseEvent<HTMLAnchorElement, MouseEvent>
        | React.KeyboardEvent<HTMLAnchorElement>,
    ) => {
      e.preventDefault();
      handlePageChange(page);
    };
  }
  function handlePageChangeOnSelectElement(
    e: React.ChangeEvent<HTMLSelectElement>,
  ) {
    const page = e.target.value;
    handlePageChange(Number(page));
  }

  return (
    <Box as="nav">
      <List as={HStack} justifyContent="center" borderBottomWidth={2} py={4}>
        <ListItem as={LinkBox}>
          {shouldShowFirst && (
            <HStack as={LinkBox}>
              <Icon fontSize={24} color="cyan.800" as={BsChevronDoubleLeft} />
              <Link
                href=""
                overlay
                onClick={pageChangeOnAnchorElementHandler(1)}
              >
                <Hide below="sm">First</Hide>
              </Link>
            </HStack>
          )}
        </ListItem>
        <ListItem as={LinkBox}>
          {shouldShowPrev && (
            <HStack as={LinkBox}>
              <Icon fontSize={20} color="cyan.800" as={BsChevronLeft} />
              <Link
                href=""
                overlay
                onClick={pageChangeOnAnchorElementHandler(currentIndex - 1)}
              >
                <Hide below="sm">Prev</Hide>
              </Link>
            </HStack>
          )}
        </ListItem>
        <ListItem>
          <Select
            placeholder="-"
            value={page}
            onChange={handlePageChangeOnSelectElement}
          >
            {[...Array(lastPage).keys()].map((i) => (
              <option key={i} value={i + 1}>
                {i + 1}
              </option>
            ))}
          </Select>
        </ListItem>
        <ListItem>
          {shouldShowNext && (
            <HStack as={LinkBox}>
              <Link
                href=""
                overlay
                onClick={pageChangeOnAnchorElementHandler(currentIndex + 1)}
              >
                <Hide below="sm">Next</Hide>
              </Link>
              <Icon fontSize={20} color="cyan.800" as={BsChevronRight} />
            </HStack>
          )}
        </ListItem>
        <ListItem>
          {shouldShowLast && (
            <HStack as={LinkBox}>
              <Link
                href=""
                overlay
                onClick={pageChangeOnAnchorElementHandler(lastPage)}
              >
                <Hide below="sm">Last</Hide>
              </Link>
              <Icon fontSize={24} color="cyan.800" as={BsChevronDoubleRight} />
            </HStack>
          )}
        </ListItem>
      </List>
    </Box>
  );
};
